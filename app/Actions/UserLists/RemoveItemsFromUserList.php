<?php

namespace AnimeSite\Actions\UserLists;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;
use AnimeSite\Models\UserList;

class RemoveItemsFromUserList
{
    /**
     * Масово видалити об'єкти зі списку користувача.
     *
     * @param User $user
     * @param array{
     *     type: string,
     *     listable_type: string,
     *     items: array<string>
     * } $data
     * @return int
     */
    public function __invoke(User $user, array $data): int
    {
        // Перевіряємо права доступу
        Gate::authorize('delete', UserList::class);
        Gate::authorize('update', $user);

        return DB::transaction(function () use ($user, $data) {
            // Видаляємо всі записи, які відповідають критеріям
            return UserList::where('user_id', $user->id)
                ->where('type', $data['type'])
                ->where('listable_type', $data['listable_type'])
                ->whereIn('listable_id', $data['items'])
                ->delete();
        });
    }
}
