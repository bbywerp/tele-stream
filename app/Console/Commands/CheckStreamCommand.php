<?php

namespace App\Console\Commands;

use App\Services\AutopostApi;
use Illuminate\Console\Command;
use RuntimeException;

class CheckStreamCommand extends Command
{
    protected $signature = 'stream:check {uuid : UUID video dari bagian /watch/UUID, tanpa query string}';

    protected $description = 'Periksa pengambilan data video dari tele-autoposts tanpa menampilkan token';

    public function handle(AutopostApi $api): int
    {
        $uuid = $this->argument('uuid');
        if (! is_string($uuid) || ! preg_match('/\A[A-Za-z0-9_-]{1,255}\z/', $uuid)) {
            $this->error('Masukkan UUID video saja, tanpa URL atau query string.');

            return self::FAILURE;
        }

        try {
            $video = $api->video($uuid);
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($video === null) {
            $this->error('API sumber mengembalikan HTTP 404. Video tidak ditemukan, series tidak aktif, atau GUID Bunny belum valid.');

            return self::FAILURE;
        }

        $this->info('Koneksi API dan data video valid. URL pemutar lolos validasi; pemutaran Bunny belum diuji oleh perintah ini.');

        return self::SUCCESS;
    }
}
