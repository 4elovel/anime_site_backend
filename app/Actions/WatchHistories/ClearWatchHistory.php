<?php

namespace AnimeSite\Actions\WatchHistories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\WatchHistory;

class ClearWatchHistory
{
    /**
     * Очистити всю історію переглядів для авторизованого користувача.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $userId = Auth::id();
        
        // Перевіряємо, чи користувач авторизований
        if (!$userId) {
            throw new \Exception('Користувач не авторизований');
        }

        DB::transaction(function () use ($userId) {
            // Видаляємо всі записи історії переглядів для користувача
            WatchHistory::where('user_id', $userId)->delete();
        });
    }
}
