<?php

namespace AnimeSite\Actions\WatchHistories;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\WatchHistory;

class ShowWatchHistory
{
    /**
     * Отримати конкретний запис історії переглядів.
     *
     * @param WatchHistory $watchHistory
     * @return WatchHistory
     */
    public function __invoke(WatchHistory $watchHistory): WatchHistory
    {
        Gate::authorize('view', $watchHistory);

        return $watchHistory->loadMissing(['user', 'episode', 'episode.anime']);
    }
}
