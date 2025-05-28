<?php

namespace AnimeSite\Actions\WatchHistories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\WatchHistory;

class UpdateWatchHistory
{
    /**
     * Оновити запис історії переглядів.
     *
     * @param WatchHistory $watchHistory
     * @param array{
     *     progress_time?: int,
     *     episode_id?: string
     * } $data
     * @return WatchHistory
     */
    public function __invoke(WatchHistory $watchHistory, array $data): WatchHistory
    {
        Gate::authorize('update', $watchHistory);

        return DB::transaction(function () use ($watchHistory, $data) {
            // Якщо змінюється епізод, перевіряємо на існування запису
            if (isset($data['episode_id']) && $data['episode_id'] !== $watchHistory->episode_id) {
                $existingRecord = WatchHistory::where([
                    'user_id' => $watchHistory->user_id,
                    'episode_id' => $data['episode_id'],
                ])->first();

                if ($existingRecord) {
                    // Якщо запис для нового епізоду вже існує, оновлюємо прогрес
                    $existingRecord->update([
                        'progress_time' => $data['progress_time'] ?? $watchHistory->progress_time,
                    ]);

                    // Видаляємо поточний запис
                    $watchHistory->delete();

                    return $existingRecord->loadMissing(['user', 'episode', 'episode.anime']);
                }
            }

            // Оновлюємо запис
            $watchHistory->update($data);

            // Завантажуємо зв'язані дані
            return $watchHistory->loadMissing(['user', 'episode', 'episode.anime']);
        });
    }
}
