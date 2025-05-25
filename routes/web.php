<?php

use Illuminate\Support\Facades\Route;

// Main welcome page
Route::get('/', function () {
    return response()->json(['status' => 'ok', 'laravel' => config('app')]);
});

// API Documentation
Route::get('/docs', function () {
    return view('scribe.index');
});

