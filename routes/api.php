<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Liamtseva\Cinema\Http\Controllers\Auth\AuthController;

// Use the sanctum middleware group
Route::middleware(['web'])->group(function () {
    // Debug endpoint
    Route::get('/debug-session', function (Request $request) {
        return response()->json([
            'has_session' => $request->hasSession(),
            'session_status' => $request->session()->isStarted() ? 'started' : 'not started',
            'authenticated' => Auth::check(),
            'cookies' => $request->cookies->all(),
        ]);
    });

    // Auth routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
