<?php

use App\BeatmapType;
use App\Http\Controllers\HttpClientController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'beatmapType' => BeatmapType::cases(),
    ]);
});

Route::post('/get-user', [HttpClientController::class,'GetUser'])->name('get-user')->middleware(EnsureTokenIsValid::class);
Route::post('/get-beatmaps', [HttpClientController::class,'GetUserBeatmaps'])->name('get-user-beatmaps')->middleware(EnsureTokenIsValid::class);
Route::post('/get-beatmap', [HttpClientController::class,'GetBeatmap'])->name('get-beatmap')->middleware(EnsureTokenIsValid::class);
