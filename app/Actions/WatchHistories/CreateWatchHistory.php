<?php

namespace AnimeSite\Actions\WatchHistories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\WatchHistory;

class CreateWatchHistory
{
    /**
     * @param array{
     *     user_id: string,
     *     episode_id: string,
     *     progress_time: int
     * } $data
     */
    public function __invoke(array $data): WatchHistory
    {
        Gate::authorize('create', WatchHistory::class);

        return DB::transaction(fn () =>
        WatchHistory::create($data)
        );
    }
}
