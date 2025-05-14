<?php

namespace AnimeSite\Actions\WatchHistories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\WatchHistory;

class UpdateWatchHistory
{
    /**
     * @param WatchHistory $watchHistory
     * @param array{
     *     progress_time?: int
     * } $data
     */
    public function __invoke(WatchHistory $watchHistory, array $data): WatchHistory
    {
        Gate::authorize('update', $watchHistory);

        return DB::transaction(function () use ($watchHistory, $data) {
            $watchHistory->update($data);
            return $watchHistory->loadMissing(['user', 'episode']);
        });
    }
}
