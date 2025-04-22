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
