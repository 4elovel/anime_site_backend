<?php

namespace AnimeSite\Actions\WatchHistories;

use AnimeSite\Models\WatchHistory;

class CleanOldWatchHistory
{
    public function __invoke(int $userId, int $days = 30): void
    {
        WatchHistory::cleanOldHistory($userId, $days);
    }
}
