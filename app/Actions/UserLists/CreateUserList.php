<?php

namespace AnimeSite\Actions\UserLists;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserList;

class CreateUserList
{
    /**
     * Створити новий список користувача.
     *
     * @param array{
     *     user_id: string,
     *     listable_type: string,
     *     listable_id: string,
     *     type: string
     * } $data
     * @return UserList
     */
    public function __invoke(array $data): UserList
    {
        // Перевіряємо права доступу
        Gate::authorize('create', UserList::class);

        return DB::transaction(function () use ($data) {
            // Перевіряємо, чи вже існує такий запис
            $existingList = UserList::where([
                'user_id' => $data['user_id'],
                'listable_type' => $data['listable_type'],
                'listable_id' => $data['listable_id'],
                'type' => $data['type'],
            ])->first();

            // Якщо запис вже існує, повертаємо його
            if ($existingList) {
                return $existingList->loadMissing(['listable', 'user']);
            }

            // Створюємо новий запис
            $userList = UserList::create($data);

            // Завантажуємо зв'язані дані
            return $userList->loadMissing(['listable', 'user']);
        });
    }
}
