<?php

namespace Liamtseva\Cinema\Console\Commands;

use Illuminate\Console\Command;
use Liamtseva\Cinema\Models\Episode;
use Liamtseva\Cinema\Services\NotificationService;
use Carbon\Carbon;

class SendEpisodeNotifications extends Command
{
    protected $signature = 'notifications:episodes';
    protected $description = 'Send notifications for episodes airing today';

    public function handle(NotificationService $notificationService): int
    {
        $today = Carbon::today();
        
        $episodes = Episode::with('anime')
            ->whereDate('air_date', $today)
            ->get();
            
        $this->info("Found {$episodes->count()} episodes airing today.");
        
        foreach ($episodes as $episode) {
            $this->info("Sending notifications for {$episode->anime->name} episode #{$episode->number}");
            $notificationService->notifyUsersAboutNewEpisode($episode);
        }
        
        return Command::SUCCESS;
    }
}