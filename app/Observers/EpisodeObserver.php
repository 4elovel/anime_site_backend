<?php

namespace Liamtseva\Cinema\Observers;

use Liamtseva\Cinema\Models\Episode;
use Liamtseva\Cinema\Services\NotificationService;

class EpisodeObserver
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function created(Episode $episode): void
    {
        // Only notify for episodes that are aired today or in the future
        if ($episode->air_date && $episode->air_date->startOfDay()->gte(now()->startOfDay())) {
            $this->notificationService->notifyUsersAboutNewEpisode($episode);
        }
    }
}