<?php

namespace AnimeSite\Actions\WatchHistories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\WatchHistory;

class DeleteWatchHistory
{
    public function __invoke(WatchHistory $watchHistory): void
    {
        Gate::authorize('delete', $watchHistory);

        DB::transaction(fn () => $watchHistory->delete());
    }
}
