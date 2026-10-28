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
        if (! is_string($base) || ! HttpUrl::valid($base)) {
            throw new RuntimeException('AUTOPOST_API_URL kosong atau bukan URL HTTP/HTTPS yang valid.');
        }
        if (! is_string($token) || $token === '') {
            throw new RuntimeException('AUTOPOST_API_TOKEN belum diisi.');
        }
        try {
            $response = Http::acceptJson()->withToken($token)->withoutRedirecting()
                ->connectTimeout(5)->timeout(15)->get(rtrim($base, '/').'/api/stream/videos/'.rawurlencode($uuid));
        } catch (Throwable $e) {
            // Only expose a numeric transport code, never the request URL or headers.
            $code = preg_match('/cURL error ([0-9]+)/', $e->getMessage(), $matches) ? ' (cURL '.$matches[1].')' : '';
            throw new RuntimeException('Sumber streaming tidak dapat dihubungi'.$code.'. Periksa DNS, TLS, dan koneksi dari VPS tele-stream.');
        }
        if ($response->status() === 404) {
            return null;
        }
        if (! $response->successful()) {
            $hint = match ($response->status()) {
                301, 302, 303, 307, 308 => 'AUTOPOST_API_URL mengalihkan permintaan; gunakan alamat akhir yang benar.',
                401 => 'Token API ditolak. Periksa kesamaan token kedua proyek dan penerusan header Authorization.',
                403 => 'Permintaan ditolak oleh server sumber atau proxy/firewall.',
                429 => 'Batas permintaan API tercapai. Coba lagi setelah jeda.',
                503 => 'Periksa konfigurasi API, pemutar Bunny, dan mode maintenance di tele-autoposts.',
                default => 'Periksa layanan API dan log tele-autoposts.',
            };
            throw new RuntimeException('API sumber mengembalikan HTTP '.$response->status().'. '.$hint);
        }
        if ($response->json('success') !== true || ! is_array($response->json('data'))) {
            throw new RuntimeException('Respons sumber bukan format API video yang diharapkan. Periksa AUTOPOST_API_URL dan rute API di tele-autoposts.');
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
            throw new RuntimeException('Data streaming tidak valid pada field: '.implode(', ', array_keys($validation->failed())).'.');
        }
        $data = $validation->validated();
        $player = parse_url($data['player_url']);
        if (($player['scheme'] ?? '') !== 'https'
            || ! in_array($player['host'] ?? '', ['iframe.mediadelivery.net', 'player.mediadelivery.net'], true)
            || ! preg_match('~\A/embed/[0-9]+/[a-fA-F0-9]{8}(?:-[a-fA-F0-9]{4}){3}-[a-fA-F0-9]{12}\z~', $player['path'] ?? '')
            || isset($player['port'])) {
            throw new RuntimeException('Alamat pemutar tidak valid.');
        }

        // The response lives only for this request: no models, sessions or content cache.
        return $data;
    }
}
