<?php

namespace Liamtseva\Cinema\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Liamtseva\Cinema\Console\Commands\SendEpisodeNotifications;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Run daily at 8:00 AM
        $schedule->command(SendEpisodeNotifications::class)->dailyAt('08:00');
        
        // Other scheduled tasks...
    }
    
    // Other methods...
}