<?php

use App\Http\Controllers\WatchController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home');
Route::get('/watch/{uuid}', WatchController::class)
    ->where('uuid', '[A-Za-z0-9_-]{1,255}')->name('watch');
