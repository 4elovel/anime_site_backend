<?php

namespace AnimeSite\Actions\WatchHistories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;
use AnimeSite\Models\WatchHistory;

class ClearUserWatchHistory
{
    /**
     * Очистити всю історію переглядів для конкретного користувача.
     *
     * @param User $user
     * @return void
     */
    public function __invoke(User $user): void
    {
        // Перевіряємо права доступу
        Gate::authorize('clearWatchHistory', $user);

        DB::transaction(function () use ($user) {
            // Видаляємо всі записи історії переглядів для користувача
            WatchHistory::where('user_id', $user->id)->delete();
        });
    }
}
