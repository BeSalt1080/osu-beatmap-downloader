<?php

use App\BeatmapCategory;
use App\Http\Controllers\HttpClientController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'beatmapCategory' => BeatmapCategory::cases(),
    ]);
});

Route::post('/get-user', [HttpClientController::class,'GetUser'])->name('get-user')->middleware(EnsureTokenIsValid::class);
Route::post('/get-user-beatmaps', [HttpClientController::class,'GetUserBeatmaps'])->name('get-user-beatmaps')->middleware(EnsureTokenIsValid::class);
Route::post('/get-user-scores', [HttpClientController::class,'GetUserScores'])->name('get-user-scores')->middleware(EnsureTokenIsValid::class);
Route::post('/get-beatmap', [HttpClientController::class,'GetBeatmap'])->name('get-beatmap')->middleware(EnsureTokenIsValid::class);
