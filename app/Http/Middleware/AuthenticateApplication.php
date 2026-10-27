<?php

namespace App\Http\Middleware;

use App\Models\IntegrationSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApplication
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = IntegrationSetting::find(1)?->api_token;
        $provided = $request->bearerToken();
        if (! $expected || ! $provided || ! hash_equals($expected, $provided)) {
            return response()->json(['success' => false, 'data' => null, 'error' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
