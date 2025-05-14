<?php

namespace AnimeSite\Actions\WatchHistories;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\WatchHistory;

class GetAllWatchHistories
{
    /**
     * Отримати список історії переглядів з фільтрацією та пагінацією.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', WatchHistory::class);

        $perPage = (int) $request->input('per_page', 15);

        return WatchHistory::query()
            ->when($request->filled('user_id'), fn($q) =>
            $q->where('user_id', $request->input('user_id'))
            )
            ->when($request->filled('episode_id'), fn($q) =>
            $q->where('episode_id', $request->input('episode_id'))
            )
            ->paginate($perPage);
    }
}
