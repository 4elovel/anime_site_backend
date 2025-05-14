<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use AnimeSite\Http\Controllers\Api\V1\AnimeController;
use AnimeSite\Http\Controllers\Api\V1\CommentController;
use AnimeSite\Http\Controllers\Api\V1\EpisodeController;
use AnimeSite\Http\Controllers\Api\V1\PersonController;
use AnimeSite\Http\Controllers\Api\V1\RatingController;
use AnimeSite\Http\Controllers\Api\V1\SearchController;
use AnimeSite\Http\Controllers\Api\V1\SelectionController;
use AnimeSite\Http\Controllers\Api\V1\StudioController;
use AnimeSite\Http\Controllers\Api\V1\TagController;
use AnimeSite\Http\Controllers\Api\V1\UserController;
use AnimeSite\Http\Controllers\Api\V1\UserListController;
use AnimeSite\Http\Controllers\Api\V1\WatchHistoryController;
use AnimeSite\Http\Controllers\Api\V1\AchievementController;
use AnimeSite\Http\Controllers\Api\V1\AuthController;
use AnimeSite\Http\Controllers\Api\V1\RecommendationController;
use AnimeSite\Http\Controllers\Api\V1\NotificationController;
use AnimeSite\Http\Controllers\Api\V1\TariffController;
use AnimeSite\Http\Controllers\Api\V1\UserSubscriptionController;
use AnimeSite\Http\Controllers\Api\V1\PaymentController;

// API Routes (v1)
Route::prefix('v1')->group(function () {
    // Public routes (no authentication required)

    // Auth routes
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    // Anime routes
    Route::prefix('animes')->group(function () {
        Route::get('/', [AnimeController::class, 'index']);
        Route::get('/trending', [AnimeController::class, 'trending']);
        Route::get('/popular', [AnimeController::class, 'popular']);
        Route::get('/current-season', [AnimeController::class, 'currentSeason']);
        Route::get('/upcoming', [AnimeController::class, 'upcoming']);
        Route::get('/{anime}', [AnimeController::class, 'show']);
        Route::get('/{anime}/episodes', [AnimeController::class, 'episodes']);
        Route::get('/{anime}/ratings', [AnimeController::class, 'ratings']);
        Route::get('/{anime}/comments', [AnimeController::class, 'comments']);
        Route::get('/{anime}/similar', [AnimeController::class, 'similar']);
        Route::get('/{anime}/related', [AnimeController::class, 'related']);
    });

    // Episode routes
    Route::prefix('episodes')->group(function () {
        Route::get('/{episode}', [EpisodeController::class, 'show']);
        Route::get('/{episode}/comments', [EpisodeController::class, 'comments']);
        Route::get('/latest', [EpisodeController::class, 'latest']);
    });

    // Person routes
    Route::prefix('people')->group(function () {
        Route::get('/', [PersonController::class, 'index']);
        Route::get('/{person}', [PersonController::class, 'show']);
        Route::get('/{person}/animes', [PersonController::class, 'animes']);
    });

    // Studio routes
    Route::prefix('studios')->group(function () {
        Route::get('/', [StudioController::class, 'index']);
        Route::get('/{studio}', [StudioController::class, 'show']);
        Route::get('/{studio}/animes', [StudioController::class, 'animes']);
    });

    // Tag routes
    Route::prefix('tags')->group(function () {
        Route::get('/', [TagController::class, 'index']);
        Route::get('/genres', [TagController::class, 'genres']);
        Route::get('/{tag}', [TagController::class, 'show']);
        Route::get('/{tag}/animes', [TagController::class, 'animes']);
    });

    // Admin/Moderator tag routes (protected by policy)
    Route::middleware('auth:sanctum')->prefix('tags')->group(function () {
        Route::post('/', [TagController::class, 'store']);
        Route::put('/{tag}', [TagController::class, 'update']);
        Route::delete('/{tag}', [TagController::class, 'destroy']);
    });

    // Selection routes (public)
    Route::prefix('selections')->group(function () {
        Route::get('/', [SelectionController::class, 'index']);
        Route::get('/featured', [SelectionController::class, 'featured']);
        Route::get('/{selection}', [SelectionController::class, 'show']); // TODO: refactor список аніме персон і епізодів
    });

    // Search route
    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/search/suggestions', [SearchController::class, 'suggestions']);

    // Tariff routes (public)
    Route::prefix('tariffs')->group(function () {
        Route::get('/', [TariffController::class, 'index']);
        Route::get('/{tariff}', [TariffController::class, 'show']);
    });

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // User routes
        Route::prefix('users')->group(function () {
            Route::get('/me', [UserController::class, 'me']);
            Route::put('/me', [UserController::class, 'updateMe']);
            Route::get('/{user}', [UserController::class, 'show']);
            // Users can only update their own profile, controlled by policy
            Route::put('/me/settings', [UserController::class, 'updateSettings']);
            Route::get('/me/profile', [UserController::class, 'profile']);
            Route::put('/me/profile', [UserController::class, 'updateProfile']);
            // Routes for uploading user avatar and backdrop
            Route::post('/me/avatar', [UserController::class, 'uploadAvatar']);
            Route::post('/me/backdrop', [UserController::class, 'uploadBackdrop']);
        });

        // Rating routes - users can rate anime
        Route::prefix('ratings')->group(function () {
            Route::get('/', [RatingController::class, 'index']);
            Route::post('/', [RatingController::class, 'store']);
            Route::get('/{rating}', [RatingController::class, 'show']);
            Route::put('/{rating}', [RatingController::class, 'update']);
            Route::delete('/{rating}', [RatingController::class, 'destroy']);
        });

        // Comment routes - users can comment on content
        Route::prefix('comments')->group(function () {
            Route::get('/', [CommentController::class, 'index']);
            Route::post('/', [CommentController::class, 'store']);
            Route::get('/{comment}', [CommentController::class, 'show']);
            Route::put('/{comment}', [CommentController::class, 'update']);
            Route::delete('/{comment}', [CommentController::class, 'destroy']);
            Route::post('/{comment}/like', [CommentController::class, 'like']);
            Route::delete('/{comment}/like', [CommentController::class, 'unlike']);
            Route::post('/{comment}/report', [CommentController::class, 'report']);
        });

        // User List routes - personal lists for users
        Route::prefix('user-lists')->group(function () {
            Route::get('/', [UserListController::class, 'index']);
            Route::post('/', [UserListController::class, 'store']);
            Route::get('/{userList}', [UserListController::class, 'show']);
            Route::put('/{userList}', [UserListController::class, 'update']);
            Route::delete('/{userList}', [UserListController::class, 'destroy']);
            Route::get('/type/{type}', [UserListController::class, 'byType']);
        });

        // Watch History routes - track user's watch history
        Route::prefix('watch-history')->group(function () {
            Route::get('/', [WatchHistoryController::class, 'index']);
            Route::post('/', [WatchHistoryController::class, 'store']);
            Route::get('/{watchHistory}', [WatchHistoryController::class, 'show']);
            Route::put('/{watchHistory}', [WatchHistoryController::class, 'update']);
            Route::delete('/{watchHistory}', [WatchHistoryController::class, 'destroy']);
            Route::delete('/clear', [WatchHistoryController::class, 'clear']);
        });

        // Achievement routes - view user achievements
        Route::prefix('achievements')->group(function () {
            Route::get('/', [AchievementController::class, 'index']);
            Route::get('/{achievement}', [AchievementController::class, 'show']);
            Route::get('/user/{user}', [AchievementController::class, 'userAchievements']);
        });

        // Selection routes - users can create personal selections
        Route::prefix('selections')->group(function () {
            Route::post('/', [SelectionController::class, 'store']);
            Route::put('/{selection}', [SelectionController::class, 'update']);
            Route::delete('/{selection}', [SelectionController::class, 'destroy']);
            Route::post('/{selection}/items', [SelectionController::class, 'addItems']);
            Route::delete('/{selection}/items', [SelectionController::class, 'removeItems']);
        });

        // Recommendation routes
        Route::prefix('recommendations')->group(function () { //в ласт чергу
            Route::get('/', [RecommendationController::class, 'index']);
            Route::get('/personalized', [RecommendationController::class, 'personalized']);
            Route::get('/based-on/{anime}', [RecommendationController::class, 'basedOnAnime']);
            Route::get('/based-on-history', [RecommendationController::class, 'basedOnHistory']);
        });

        // Notification routes
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread', [NotificationController::class, 'unread']);
            Route::put('/{notification}/read', [NotificationController::class, 'markAsRead']);
            Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
            Route::delete('/{notification}', [NotificationController::class, 'destroy']);
            Route::delete('/clear', [NotificationController::class, 'clear']);
        });

        // Subscription routes
        Route::prefix('subscriptions')->group(function () {
            Route::get('/', [UserSubscriptionController::class, 'index']);
            Route::get('/active', [UserSubscriptionController::class, 'active']);
            Route::get('/{subscription}', [UserSubscriptionController::class, 'show']);
            Route::post('/', [UserSubscriptionController::class, 'store']);
            Route::put('/{subscription}/cancel', [UserSubscriptionController::class, 'cancel']);
            Route::put('/{subscription}/renew', [UserSubscriptionController::class, 'renew']);
        });

        // Payment routes
        Route::prefix('payments')->group(function () {
            Route::get('/', [PaymentController::class, 'index']);
            Route::get('/{payment}', [PaymentController::class, 'show']);
            Route::post('/', [PaymentController::class, 'store']);
            Route::post('/callback', [PaymentController::class, 'callback']);
            Route::get('/status/{transaction_id}', [PaymentController::class, 'checkStatus']);
        });
    });
});
