<?php

namespace Liamtseva\Cinema\Services;

use Liamtseva\Cinema\Models\Episode;
use Liamtseva\Cinema\Models\User;
use Liamtseva\Cinema\Notifications\NewEpisodeNotification;

class NotificationService
{
    public function notifyUsersAboutNewEpisode(Episode $episode): void
    {
        $anime = $episode->anime;
        
        // Get all users subscribed to this anime
        $subscribedUsers = $anime->userNotifications;
        
        foreach ($subscribedUsers as $user) {
            $user->notify(new NewEpisodeNotification($episode));
        }
    }
}