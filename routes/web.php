<?php

use App\Http\Controllers\WatchController;
use App\Http\Middleware\RequireServerRedirect;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response('', 404)->header('Cache-Control', 'private, no-store'));
Route::get('/watch/{uuid}', WatchController::class)
    ->where('uuid', '[A-Za-z0-9_-]{1,255}')->middleware(RequireServerRedirect::class)->name('watch');
