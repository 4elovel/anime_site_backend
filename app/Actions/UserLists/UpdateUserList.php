<?php

namespace AnimeSite\Actions\UserLists;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserList;

class UpdateUserList
{
    /**
     * Оновити список користувача.
     *
     * @param UserList $userList
     * @param array{
     *     listable_type?: string,
     *     listable_id?: string,
     *     type?: string
     * } $data
     * @return UserList
     */
    public function __invoke(UserList $userList, array $data): UserList
    {
        // Перевіряємо права доступу
        Gate::authorize('update', $userList);

        return DB::transaction(function () use ($userList, $data) {
            // Перевіряємо, чи змінюється тип об'єкта або ID об'єкта
            if (isset($data['listable_type']) || isset($data['listable_id'])) {
                // Перевіряємо, чи вже існує такий запис
                $existingList = UserList::where([
                    'user_id' => $userList->user_id,
                    'listable_type' => $data['listable_type'] ?? $userList->listable_type,
                    'listable_id' => $data['listable_id'] ?? $userList->listable_id,
                    'type' => $data['type'] ?? $userList->type,
                ])->where('id', '!=', $userList->id)->first();

                // Якщо запис вже існує, викидаємо помилку
                if ($existingList) {
                    throw new \Exception('Такий запис вже існує в списку');
                }
            }

            // Оновлюємо запис
            $userList->update($data);

            // Завантажуємо зв'язані дані
            return $userList->loadMissing(['listable', 'user']);
        });
    }
}
