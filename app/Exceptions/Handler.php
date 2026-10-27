<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($request->is('api/*')) {
            $request->headers->set('Accept', 'application/json');
            $response = parent::render($request, $e);
            $status = $response->getStatusCode();
            $message = match ($status) {
                401 => 'Unauthorized.', 404 => 'Konten tidak ditemukan.',
                422 => 'Parameter tidak valid.', 429 => 'Terlalu banyak permintaan.',
                default => 'Permintaan API gagal.',
            };

            return response()->json(['success' => false, 'data' => null, 'error' => $message], $status)->withHeaders($status === 429 ? ['Retry-After' => $response->headers->get('Retry-After', '60')] : []);
        }

        return parent::render($request, $e);
    }

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'api_token',
        'telegram_token',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
