<?php

use App\Http\Controllers\WatchController;
use App\Http\Middleware\RequireServerRedirect;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response('', 404)->header('Cache-Control', 'private, no-store'));
Route::get('/ads.webp', function () {
    $path = storage_path('app/public/ads.webp');
    abort_unless(is_file($path), 404);

    return response()->file($path, ['Content-Type' => 'image/webp']);
})->name('ads.image');
Route::get('/watch/{uuid}', WatchController::class)
    ->where('uuid', '[A-Za-z0-9_-]{1,255}')->middleware(RequireServerRedirect::class)->name('watch');
