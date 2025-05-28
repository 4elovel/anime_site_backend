<?php

namespace AnimeSite\Actions\WatchHistories;

use Illuminate\Support\Facades\DB;
use AnimeSite\Models\WatchHistory;

class CleanOldWatchHistory
{
    /**
     * Очистити стару історію переглядів для користувача.
     *
     * @param string $userId
     * @param int $days
     * @return void
     */
    public function __invoke(string $userId, int $days = 30): void
    {
        DB::transaction(function () use ($userId, $days) {
            // Використовуємо метод моделі для очищення старої історії
            WatchHistory::cleanOldHistory($userId, $days);
        });
    }
}
