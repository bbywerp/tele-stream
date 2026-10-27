<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireServerRedirect
{
    public function handle(Request $request, Closure $next)
    {
        $token = config('autopost.token');
        if (! is_string($token) || $token === '') {
            return $this->deny(503);
        }

        $expires = $request->query('expires');
        $signature = $request->query('signature');
        $now = now()->timestamp;
        if (! is_string($expires) || ! preg_match('/\A[0-9]{1,10}\z/', $expires)
            || (int) $expires <= $now || (int) $expires > $now + 300
            || ! is_string($signature) || ! preg_match('/\A[a-f0-9]{64}\z/', $signature)) {
            return $this->deny(403);
        }

        $uuid = $request->route('uuid');
        $expected = hash_hmac('sha256', "tele-stream-entry:v1\n{$uuid}\n{$expires}", $token);
        if (! hash_equals($expected, $signature)) {
            return $this->deny(403);
        }

        return $next($request);
    }

    private function deny(int $status)
    {
        return response('', $status)->header('Cache-Control', 'private, no-store')
            ->header('Referrer-Policy', 'no-referrer');
    }
}
