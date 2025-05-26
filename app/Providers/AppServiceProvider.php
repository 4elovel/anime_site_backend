<?php

namespace Liamtseva\Cinema\Providers;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Liamtseva\Cinema\Models\Episode;
use Liamtseva\Cinema\Notifications\Channels\CustomDatabaseChannel;
use Liamtseva\Cinema\Observers\EpisodeObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Register observers
        Episode::observe(EpisodeObserver::class);

        // Register custom notification channel
        Notification::extend('custom-database', function ($app) {
            return new CustomDatabaseChannel();
        });

        // Other boot code...
    }
}
