<?php

namespace Tests\Feature;

use Illuminate\Cache\ArrayStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StreamingTest extends TestCase
{
    private string $uuid = 'source-video-0047';

    private string $token = 'private-server-api-token';

    protected function setUp(): void
    {
        parent::setUp();
        config(['autopost.url' => 'https://posts.example', 'autopost.token' => $this->token,
            'database.default' => 'no_database_available']);
        DB::shouldReceive('connection')->never();
        Storage::fake('local');
    }

    private function signedWatchUrl(?string $uuid = null, ?int $expires = null, ?string $token = null): string
    {
        $uuid ??= $this->uuid;
        $expires ??= now()->addMinutes(5)->timestamp;
        $signature = hash_hmac('sha256', "tele-stream-entry:v1\n{$uuid}\n{$expires}", $token ?? $this->token);

        return '/watch/'.$uuid.'?'.http_build_query(['expires' => $expires, 'signature' => $signature]);
    }

    private function payload(): array
    {
        return ['success' => true, 'error' => null, 'data' => [
            'uuid' => $this->uuid, 'title' => '<script>Video title</script>', 'description' => 'Dari tele-autoposts',
            'thumbnail_url' => null, 'series' => ['uuid' => 'series0001', 'title' => 'Series sumber'],
            'player_url' => 'https://player.mediadelivery.net/embed/714130/419241bf-cc2e-4eb2-9203-4c082c08613c?token=temporary-token&expires=1999999999',
        ]];
    }

    public function test_home_and_removed_management_routes_need_no_database_or_sessions(): void
    {
        $this->get('/')->assertNotFound()->assertContent('')->assertHeaderMissing('Set-Cookie');
        foreach (['/login', '/register', '/dashboard', '/dashboard/series', '/dashboard/settings/api', '/api/series', '/api/stats'] as $url) {
            $this->get($url)->assertNotFound();
        }
        Http::assertNothingSent();
        $this->assertInstanceOf(ArrayStore::class, Cache::store()->getStore());
    }

    public function test_watch_fetches_fresh_api_data_for_each_request_and_keeps_it_out_of_storage(): void
    {
        Http::fake(['posts.example/*' => Http::response($this->payload())]);
        $response = $this->get($this->signedWatchUrl())->assertOk()->assertSee('Series sumber')
            ->assertSee('&lt;script&gt;Video title&lt;/script&gt;', false)->assertSee('player.mediadelivery.net/embed/')
            ->assertDontSee($this->token)->assertHeaderMissing('Set-Cookie')->assertHeader('Cache-Control', 'no-store, private');
        $this->assertStringNotContainsString('<script>Video title</script>', $response->getContent());
        $this->get($this->signedWatchUrl())->assertOk();
        Http::assertSentCount(2);
        Http::assertSent(fn ($request) => $request->url() === 'https://posts.example/api/stream/videos/'.$this->uuid
            && $request->hasHeader('Authorization', 'Bearer '.$this->token));
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_direct_links_are_rejected_even_with_a_forged_server_referrer(): void
    {
        $this->get('/watch/'.$this->uuid)->assertForbidden()->assertContent('')
            ->assertHeader('Cache-Control', 'no-store, private')->assertHeaderMissing('Set-Cookie');
        $this->withHeader('Referer', 'https://posts.example/p/abcd1234')
            ->get('/watch/'.$this->uuid)->assertForbidden()->assertContent('');
        Http::assertNothingSent();
    }

    public function test_expired_tampered_or_malformed_redirects_never_fetch_video_data(): void
    {
        $this->freezeTime();
        $validUrl = $this->signedWatchUrl();
        $urls = [
            $this->signedWatchUrl(expires: now()->timestamp),
            $this->signedWatchUrl(expires: now()->timestamp - 1),
            $this->signedWatchUrl(expires: now()->timestamp + 301),
            $this->signedWatchUrl(token: 'wrong-secret'),
            str_replace($this->uuid, 'another-video', $validUrl),
            str_replace('expires='.now()->addMinutes(5)->timestamp, 'expires='.(now()->timestamp + 10), $validUrl),
            '/watch/'.$this->uuid.'?expires[]=123&signature[]=abc',
            '/watch/'.$this->uuid.'?expires=not-a-number&signature='.str_repeat('a', 64),
            '/watch/'.$this->uuid.'?expires='.now()->addMinutes(5)->timestamp.'&signature=invalid',
        ];
        foreach ($urls as $url) {
            $this->get($url)->assertForbidden()->assertContent('')->assertHeaderMissing('Set-Cookie');
        }
        $this->travel(301)->seconds();
        $this->get($validUrl)->assertForbidden()->assertContent('');
        Http::assertNothingSent();
    }

    public function test_remote_missing_content_returns_404(): void
    {
        Http::fake(['posts.example/*' => Http::response(['success' => false], 404)]);
        $this->get($this->signedWatchUrl())->assertNotFound()->assertSee('Video tidak ditemukan')->assertHeaderMissing('Set-Cookie');
    }

    public function test_api_errors_and_timeouts_return_a_generic_service_error(): void
    {
        Http::fake(['posts.example/*' => Http::response(['private' => $this->token], 500)]);
        $this->get($this->signedWatchUrl())->assertStatus(503)->assertSee('Konten belum dapat dimuat')->assertDontSee($this->token);
        Http::swap(new \Illuminate\Http\Client\Factory);
        Http::preventStrayRequests();
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Secret '.$this->token));
        $this->get($this->signedWatchUrl())->assertStatus(503)->assertDontSee($this->token);
    }

    public function test_wrong_video_or_untrusted_player_urls_cannot_be_rendered(): void
    {
        $payload = $this->payload();
        $payload['data']['player_url'] = 'https://attacker.example/embed/714130/419241bf-cc2e-4eb2-9203-4c082c08613c';
        Http::fake(['posts.example/*' => Http::response($payload)]);
        $this->get($this->signedWatchUrl())->assertStatus(503)->assertDontSee('attacker.example');
        $payload = $this->payload();
        $payload['data']['uuid'] = 'different-video';
        Http::swap(new \Illuminate\Http\Client\Factory);
        Http::preventStrayRequests();
        Http::fake(['posts.example/*' => Http::response($payload)]);
        $this->get($this->signedWatchUrl())->assertStatus(503);
    }

    public function test_missing_api_configuration_fails_without_using_a_database(): void
    {
        config(['autopost.url' => null]);
        $this->get($this->signedWatchUrl())->assertStatus(503);
        Http::assertNothingSent();
    }

    public function test_missing_api_token_prevents_an_unauthenticated_request(): void
    {
        config(['autopost.token' => null]);
        $this->get($this->signedWatchUrl())->assertStatus(503);
        Http::assertNothingSent();
    }
}
