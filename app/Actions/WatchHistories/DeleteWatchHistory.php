<?php

namespace AnimeSite\Actions\WatchHistories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\WatchHistory;

class DeleteWatchHistory
{
    /**
     * Видалити запис історії переглядів.
     *
     * @param WatchHistory $watchHistory
     * @return void
     */
    public function __invoke(WatchHistory $watchHistory): void
    {
        Gate::authorize('delete', $watchHistory);

        DB::transaction(function () use ($watchHistory) {
            // Видаляємо запис
            $watchHistory->delete();
        });
    }
}
