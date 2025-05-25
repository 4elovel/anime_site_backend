<?php

namespace AnimeSite\Actions\WatchHistories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\WatchHistory;

class CreateWatchHistory
{
    /**
     * Створити новий запис історії переглядів.
     *
     * @param array{
     *     user_id: string,
     *     episode_id: string,
     *     progress_time: int
     * } $data
     * @return WatchHistory
     */
    public function __invoke(array $data): WatchHistory
    {
        Gate::authorize('create', WatchHistory::class);

        return DB::transaction(function () use ($data) {
            // Перевіряємо, чи вже існує запис для цього користувача та епізоду
            $existingRecord = WatchHistory::where([
                'user_id' => $data['user_id'],
                'episode_id' => $data['episode_id'],
            ])->first();

            if ($existingRecord) {
                // Якщо запис вже існує, оновлюємо прогрес
                $existingRecord->update([
                    'progress_time' => $data['progress_time'],
                ]);

                return $existingRecord->loadMissing(['user', 'episode', 'episode.anime']);
            }

            // Створюємо новий запис
            $watchHistory = WatchHistory::create($data);

            // Завантажуємо зв'язані дані
            return $watchHistory->loadMissing(['user', 'episode', 'episode.anime']);
        });
    }
}
