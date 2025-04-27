<?php

use Illuminate\Support\Facades\Route;
use Liamtseva\Cinema\Http\Controllers\AnimeController;
use Liamtseva\Cinema\Http\Controllers\SelectionController;
use Liamtseva\Cinema\Http\Controllers\StudioController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/selections', [SelectionController::class, 'index']);
//Route::get('/studios', [StudioController::class, 'index']);
Route::resource('anime', AnimeController::class);


Route::resource('studios', StudioController::class);
//Route::resource('movies.comments', CommentController::class);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
