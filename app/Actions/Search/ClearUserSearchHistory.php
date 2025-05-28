<?php

namespace AnimeSite\Actions\Search;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\SearchHistory;
use AnimeSite\Models\User;

class ClearUserSearchHistory
{
    /**
     * Очистити історію пошуку користувача.
     *
     * @param User $user
     * @return void
     */
    public function __invoke(User $user): void
    {
        // Перевіряємо права доступу
        Gate::authorize('clearSearchHistory', $user);

        DB::transaction(function () use ($user) {
            // Видаляємо всі записи історії пошуку користувача
            SearchHistory::where('user_id', $user->id)->delete();
        });
    }
}
