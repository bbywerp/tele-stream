<?php

namespace Tests\Feature;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StreamCheckTest extends TestCase
{
    private string $token = 'private-diagnostic-api-test-token';

    protected function setUp(): void
    {
        parent::setUp();
        config(['autopost.url' => 'https://posts.example', 'autopost.token' => $this->token,
            'database.default' => 'no_database_available']);
        DB::shouldReceive('connection')->never();
    }

    private function payload(): array
    {
        return ['success' => true, 'data' => ['uuid' => 'source-video', 'title' => 'Private video title',
            'series' => ['uuid' => 'series0001', 'title' => 'Private series title'],
            'player_url' => 'https://iframe.mediadelivery.net/embed/714130/419241bf-cc2e-4eb2-9203-4c082c08613c?token=private-player-test-token',
        ]];
    }

    public function test_command_checks_the_same_api_and_does_not_print_video_or_credentials(): void
    {
        Http::fake(['posts.example/*' => Http::response($this->payload())]);
        $this->artisan('stream:check', ['uuid' => 'source-video'])->expectsOutputToContain('Koneksi API dan data video valid.')
            ->doesntExpectOutputToContain($this->token)->doesntExpectOutputToContain('private-player-test-token')
            ->doesntExpectOutputToContain('Private video title')->assertSuccessful();
        Http::assertSent(fn ($request) => $request->url() === 'https://posts.example/api/stream/videos/source-video'
            && $request->hasHeader('Authorization', 'Bearer '.$this->token));
        Http::assertSentCount(1);
    }

    public function test_missing_configuration_is_reported_without_a_request(): void
    {
        config(['autopost.url' => null]);
        $this->artisan('stream:check', ['uuid' => 'source-video'])->expectsOutputToContain('AUTOPOST_API_URL kosong')
            ->assertFailed();
        config(['autopost.url' => 'https://posts.example', 'autopost.token' => null]);
        $this->artisan('stream:check', ['uuid' => 'source-video'])->expectsOutput('AUTOPOST_API_TOKEN belum diisi.')
            ->assertFailed();
        Http::assertNothingSent();
    }

    public static function sourceErrors(): array
    {
        return array_map(fn ($status) => [$status], [302, 401, 403, 404, 429, 500, 503]);
    }

    /** @dataProvider sourceErrors */
    public function test_http_status_is_reported_without_printing_the_remote_response(int $status): void
    {
        Http::fake(['posts.example/*' => Http::response(['error' => $this->token], $status,
            $status === 302 ? ['Location' => 'https://redirect.example/'.$this->token] : [])]);
        $this->artisan('stream:check', ['uuid' => 'source-video'])->expectsOutputToContain('HTTP '.$status)
            ->doesntExpectOutputToContain($this->token)->assertFailed();
        Http::assertSentCount(1);
    }

    public function test_transport_failure_only_reports_numeric_curl_code(): void
    {
        Http::fake(fn () => throw new ConnectionException('cURL error 60: certificate details '.$this->token));
        $this->artisan('stream:check', ['uuid' => 'source-video'])->expectsOutputToContain('(cURL 60)')
            ->doesntExpectOutputToContain($this->token)->doesntExpectOutputToContain('certificate details')->assertFailed();
    }

    public function test_invalid_data_reports_field_names_without_values(): void
    {
        $payload = $this->payload();
        $payload['data']['uuid'] = $this->token;
        Http::fake(['posts.example/*' => Http::response($payload)]);
        $this->artisan('stream:check', ['uuid' => 'source-video'])->expectsOutput('Data streaming tidak valid pada field: uuid.')
            ->doesntExpectOutputToContain($this->token)->assertFailed();
    }

    public function test_an_html_response_is_identified_as_the_wrong_api_format(): void
    {
        Http::fake(['posts.example/*' => Http::response('<html>'.$this->token.'</html>')]);
        $this->artisan('stream:check', ['uuid' => 'source-video'])->expectsOutputToContain('bukan format API video')
            ->doesntExpectOutputToContain($this->token)->assertFailed();
    }

    public function test_invalid_arguments_do_not_make_a_request_or_print_a_signed_url(): void
    {
        $this->artisan('stream:check', ['uuid' => 'https://stream.example/watch/source-video?signature='.$this->token])
            ->expectsOutput('Masukkan UUID video saja, tanpa URL atau query string.')
            ->doesntExpectOutputToContain($this->token)->assertFailed();
        Http::assertNothingSent();
    }
}
