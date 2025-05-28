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
            // Фільтрація за користувачем
            ->when($request->filled('user_id'), fn($q) =>
                $q->where('user_id', $request->input('user_id'))
            )
            // Фільтрація за епізодом
            ->when($request->filled('episode_id'), fn($q) =>
                $q->where('episode_id', $request->input('episode_id'))
            )
            // Фільтрація за аніме
            ->when($request->filled('anime_id'), fn($q) =>
                $q->whereHas('episode', function ($query) use ($request) {
                    $query->where('anime_id', $request->input('anime_id'));
                })
            )
            // Фільтрація за прогресом
            ->when($request->filled('min_progress') && $request->filled('max_progress'), fn($q) =>
                $q->whereBetween('progress_time', [
                    $request->input('min_progress'),
                    $request->input('max_progress')
                ])
            )
            // Фільтрація за датою
            ->when($request->filled('date_from') && $request->filled('date_to'), fn($q) =>
                $q->whereBetween('created_at', [
                    $request->input('date_from'),
                    $request->input('date_to')
                ])
            )
            // Сортування
            ->when($request->filled('sort'), function ($query) use ($request) {
                $sort = $request->input('sort');
                $direction = 'asc';

                if (str_starts_with($sort, '-')) {
                    $direction = 'desc';
                    $sort = substr($sort, 1);
                }

                if (in_array($sort, ['created_at', 'updated_at', 'progress_time'])) {
                    $query->orderBy($sort, $direction);
                }
            }, fn($q) =>
                $q->orderBy('created_at', 'desc') // За замовчуванням сортуємо за датою створення (нові спочатку)
            )
            // Завантажуємо зв'язані дані
            ->with(['user', 'episode', 'episode.anime'])
            // Пагінація
            ->paginate($perPage);
    }
}
