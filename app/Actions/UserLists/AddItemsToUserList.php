<?php

namespace AnimeSite\Actions\UserLists;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;
use AnimeSite\Models\UserList;

class AddItemsToUserList
{
    /**
     * Масово додати об'єкти до списку користувача.
     *
     * @param User $user
     * @param array{
     *     type: string,
     *     listable_type: string,
     *     items: array<string>
     * } $data
     * @return array<UserList>
     */
    public function __invoke(User $user, array $data): array
    {
        // Перевіряємо права доступу
        Gate::authorize('create', UserList::class);
        Gate::authorize('update', $user);

        return DB::transaction(function () use ($user, $data) {
            $createdLists = [];
            
            // Перебираємо всі ID об'єктів
            foreach ($data['items'] as $itemId) {
                // Перевіряємо, чи вже існує такий запис
                $existingList = UserList::where([
                    'user_id' => $user->id,
                    'listable_type' => $data['listable_type'],
                    'listable_id' => $itemId,
                    'type' => $data['type'],
                ])->first();
                
                // Якщо запис вже існує, пропускаємо його
                if ($existingList) {
                    $createdLists[] = $existingList;
                    continue;
                }
                
                // Створюємо новий запис
                $userList = UserList::create([
                    'user_id' => $user->id,
                    'listable_type' => $data['listable_type'],
                    'listable_id' => $itemId,
                    'type' => $data['type'],
                ]);
                
                $createdLists[] = $userList;
            }
            
            // Завантажуємо зв'язані дані для всіх створених списків
            return array_map(function ($list) {
                return $list->loadMissing(['listable']);
            }, $createdLists);
        });
    }
}
