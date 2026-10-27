<?php

namespace App\Http\Controllers;

use App\Services\AutopostApi;
use RuntimeException;

class WatchController extends Controller
{
    public function __invoke(string $uuid, AutopostApi $api)
    {
        $unavailable = false;
        try {
            $video = $api->video($uuid);
        } catch (RuntimeException) {
            $video = null;
            $unavailable = true;
        }

        return response()->view('watch', compact('video', 'unavailable'), $unavailable ? 503 : ($video ? 200 : 404))
            ->header('Cache-Control', 'private, no-store')
            ->header('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
