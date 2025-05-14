<?php

namespace AnimeSite\Actions\WatchHistories;

use AnimeSite\Models\WatchHistory;

class ShowWatchHistory
{
    public function __invoke(WatchHistory $watchHistory): WatchHistory
    {
        Gate::authorize('view', $watchHistory);

        return $watchHistory->loadMissing(['user', 'episode']);
    }
}
