<?php

namespace Tests\Feature;

use App\Models\IntegrationSetting;
use App\Models\Series;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private string $token = 'test-token-with-at-least-32-characters';

    protected function setUp(): void
    {
        parent::setUp();
        IntegrationSetting::findOrFail(1)->update(['api_token' => $this->token]);
    }

    private function series(int $activeVideos = 3, bool $active = true): Series
    {
        $series = Series::create(['title' => 'Series pilihan', 'description' => 'Deskripsi', 'is_active' => $active]);
        for ($i = 0; $i < $activeVideos; $i++) {
            $series->videos()->create(['title' => 'Video '.$i, 'stream_url' => 'https://cdn.example.com/video.m3u8', 'is_active' => true]);
        }

        return $series;
    }

    public function test_admin_access_is_required_for_every_dashboard_page(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/dashboard')->assertForbidden();
        $this->get('/dashboard/series')->assertForbidden();
        $this->get('/dashboard/settings/api')->assertForbidden();
        $this->actingAs(User::factory()->admin()->create());
        foreach (['/dashboard', '/dashboard/series', '/dashboard/series/create', '/dashboard/settings/api'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/dashboard/series?search[]=invalid')->assertSessionHasErrors('search');
    }

    public function test_api_rejects_missing_incorrect_and_array_tokens(): void
    {
        $this->getJson('/api/series/random')->assertUnauthorized()->assertJson(['success' => false, 'data' => null]);
        $this->withToken('wrong')->getJson('/api/stats')->assertUnauthorized();
        $this->getJson('/api/stats?api_token[]=unexpected')->assertUnauthorized();
        $this->withToken($this->token)->getJson('/api/stats')->assertOk()->assertJsonPath('success', true);
        IntegrationSetting::findOrFail(1)->update(['api_token' => null]);
        $this->getJson('/api/stats')->assertUnauthorized();
    }

    public function test_random_series_is_active_has_three_active_videos_and_respects_exclusions(): void
    {
        $eligible = $this->series(4);
        $this->series(2);
        $this->series(3, false);
        $this->withToken($this->token)->getJson('/api/series/random')->assertOk()->assertJsonPath('data.uuid', $eligible->uuid)->assertDontSee('stream_url');
        $this->getJson('/api/series/random?exclude[]='.$eligible->uuid)->assertNotFound()->assertJsonPath('success', false);
        $eligible->videos()->first()->update(['is_active' => false]);
        $eligible->videos()->where('is_active', true)->first()->update(['is_active' => false]);
        $this->getJson('/api/series/random')->assertNotFound();
    }

    public function test_api_is_paginated_and_never_exposes_stream_urls_or_internal_ids(): void
    {
        $series = $this->series(51);
        $series->videos()->create(['title' => 'Tidak aktif', 'stream_url' => 'https://cdn.example.com/private.mp4', 'is_active' => false]);
        $this->withToken($this->token)->getJson('/api/series/'.$series->uuid.'/videos')->assertOk()->assertJsonCount(50, 'data')->assertJsonPath('meta.last_page', 2)->assertDontSee('stream_url')->assertDontSee('series_id');
        $this->getJson('/api/series/'.$series->uuid.'/videos?page=2')->assertJsonCount(1, 'data');
        $this->getJson('/api/series/'.$series->uuid)->assertJsonPath('data.uuid', $series->uuid)->assertDontSee('stream_url');
        $video = $series->videos()->where('is_active', true)->first();
        $this->getJson('/api/videos/'.$video->uuid)->assertOk()->assertJsonPath('data.series_uuid', $series->uuid)->assertDontSee('cdn.example.com');
        $this->getJson('/api/videos/'.$video->id)->assertNotFound()->assertJsonPath('success', false);
        $this->getJson('/api/series/random?exclude[]=invalid')->assertUnprocessable()->assertJsonPath('success', false);
    }

    public function test_watch_renders_saved_stream_without_exposing_api_token_and_hides_inactive_content(): void
    {
        $series = $this->series();
        $video = $series->videos()->first();
        $this->get('/watch/'.$video->uuid)->assertOk()->assertSee('data-stream=', false)->assertSee('https://cdn.example.com/video.m3u8')->assertDontSee($this->token);
        $video->update(['is_active' => false]);
        $this->get('/watch/'.$video->uuid)->assertNotFound()->assertSee('Video tidak ditemukan');
        $video->update(['is_active' => true]);
        $series->update(['is_active' => false]);
        $this->get('/watch/'.$video->uuid)->assertNotFound();
        $this->withToken($this->token)->getJson('/api/videos/'.$video->uuid)->assertNotFound();
        $this->get('/watch/'.Str::uuid())->assertNotFound();
    }

    public function test_admin_can_manage_series_and_video_with_stable_public_uuids(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $this->post('/dashboard/series', ['title' => '<script>test</script>', 'is_active' => 1])->assertRedirect();
        $series = Series::firstOrFail();
        $this->assertTrue(Str::isUuid($series->uuid));
        $this->get(route('series.show', $series))->assertOk()->assertSee('&lt;script&gt;test&lt;/script&gt;', false);
        $this->post(route('series.videos.store', $series), ['title' => 'Episode', 'stream_url' => 'javascript:alert(1)', 'is_active' => 1])->assertSessionHasErrors('stream_url');
        $this->post(route('series.videos.store', $series), ['title' => 'Episode', 'stream_url' => 'https://cdn.example.com/video.mp4', 'is_active' => 1])->assertRedirect();
        $video = Video::firstOrFail();
        $uuid = $video->uuid;
        $this->get(route('series.videos.edit', [$series, $video]))->assertOk();
        $this->put(route('series.videos.update', [$series, $video]), ['title' => 'Updated', 'stream_url' => 'https://cdn.example.com/new.mp4', 'is_active' => 1])->assertRedirect();
        $this->assertSame($uuid, $video->fresh()->uuid);
        $other = $this->series(0);
        $this->delete(route('series.videos.destroy', [$other, $video]))->assertNotFound();
        $this->delete(route('series.destroy', $series))->assertRedirect();
        $this->assertDatabaseMissing('videos', ['uuid' => $uuid]);
    }

    public function test_token_is_encrypted_masked_retained_when_blank_and_never_flashed_on_validation_error(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $this->assertNotSame($this->token, DB::table('integration_settings')->value('api_token'));
        $this->get('/dashboard/settings/api')->assertOk()->assertDontSee($this->token);
        $this->put('/dashboard/settings/api', ['api_token' => ''])->assertSessionHasNoErrors();
        $this->assertSame($this->token, IntegrationSetting::find(1)->api_token);
        $this->put('/dashboard/settings/api', ['api_token' => 'short'])->assertSessionHasErrors('api_token')->assertSessionMissing('_old_input.api_token');
    }
}
