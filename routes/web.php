<?php

use Illuminate\Support\Facades\Route;

// Main welcome page
Route::get('/', function () {
    return response()->json(['status' => 'ok', 'laravel' => config('app')]);
});

