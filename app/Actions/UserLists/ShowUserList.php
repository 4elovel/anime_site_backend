<?php

namespace AnimeSite\Actions\UserLists;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserList;

class ShowUserList
{
    /**
     * Отримати конкретний список користувача.
     *
     * @param UserList $userList
     * @return UserList
     */
    public function __invoke(UserList $userList): UserList
    {
        // Перевіряємо права доступу
        Gate::authorize('view', $userList);

        // Завантажуємо зв'язані дані
        return $userList->loadMissing(['listable', 'user']);
    }
}
