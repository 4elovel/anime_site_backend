<?php

namespace AnimeSite\Actions\UserLists;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserList;

class UpdateUserList
{
    /**
     * @param UserList $userList
     * @param array{
     *     listable_type?: string,
     *     listable_id?: string,
     *     type?: string
     * } $data
     */
    public function __invoke(UserList $userList, array $data): UserList
    {
        Gate::authorize('update', $userList);

        return DB::transaction(function () use ($userList, $data) {
            $userList->update($data);
            return $userList->loadMissing(['listable']);
        });
    }
}
