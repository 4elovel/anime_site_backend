<?php

namespace AnimeSite\Actions\UserLists;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserList;

class DeleteUserList
{
    /**
     * Видалити список користувача.
     *
     * @param UserList $userList
     * @return void
     */
    public function __invoke(UserList $userList): void
    {
        // Перевіряємо права доступу
        Gate::authorize('delete', $userList);

        DB::transaction(function () use ($userList) {
            // Видаляємо запис
            $userList->delete();
        });
    }
}
