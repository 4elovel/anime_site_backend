<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Liamtseva\Cinema\Http\Controllers\Auth\AuthController;
use Liamtseva\Cinema\Http\Controllers\NotificationController;

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

// Notification routes
Route::middleware('auth:sanctum')->group(function () {
    // Get user notifications with pagination
    Route::get('/notifications', [NotificationController::class, 'index']);

    // Get specific notification and mark as read
    Route::get('/notifications/{id}', [NotificationController::class, 'show']);

    // Mark notification as read
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // Mark all notifications as read
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Get unread notification count
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
});
