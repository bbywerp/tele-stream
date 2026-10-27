<?php

namespace App\Services;

use App\Rules\HttpUrl;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class AutopostApi
{
    public function video(string $uuid): ?array
    {
        $base = config('autopost.url');
        $token = config('autopost.token');
        if (! is_string($base) || ! HttpUrl::valid($base) || ! is_string($token) || $token === '') {
            throw new RuntimeException('Sumber streaming belum dikonfigurasi.');
        }
        try {
            $response = Http::acceptJson()->withToken($token)->withoutRedirecting()
                ->connectTimeout(5)->timeout(15)->get(rtrim($base, '/').'/api/stream/videos/'.rawurlencode($uuid));
        } catch (Throwable) {
            throw new RuntimeException('Sumber streaming tidak dapat dihubungi.');
        }
        if ($response->status() === 404) {
            return null;
        }
        if (! $response->successful() || $response->json('success') !== true || ! is_array($response->json('data'))) {
            throw new RuntimeException('Sumber streaming belum tersedia.');
        }
        $validation = Validator::make($response->json('data'), [
            'uuid' => ['required', 'string', Rule::in([$uuid])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:100000'],
            'thumbnail_url' => ['nullable', 'string', 'max:2048', new HttpUrl],
            'series' => ['required', 'array:uuid,title'],
            'series.uuid' => ['required', 'string', 'max:255'],
            'series.title' => ['required', 'string', 'max:255'],
            'player_url' => ['required', 'string', 'max:4096', new HttpUrl],
        ]);
        if ($validation->fails()) {
            throw new RuntimeException('Data streaming tidak valid.');
        }
        $data = $validation->validated();
        $player = parse_url($data['player_url']);
        if (($player['scheme'] ?? '') !== 'https' || ($player['host'] ?? '') !== 'player.mediadelivery.net'
            || ! preg_match('~\A/embed/[0-9]+/[a-fA-F0-9-]{36}\z~', $player['path'] ?? '') || isset($player['port'])) {
            throw new RuntimeException('Alamat pemutar tidak valid.');
        }

        // The response lives only for this request: no models, sessions or content cache.
        return $data;
    }
}
