<?php

namespace AnimeSite\Actions\UserLists;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserList;

class DeleteUserList
{
    public function __invoke(UserList $userList): void
    {
        Gate::authorize('delete', $userList);

        DB::transaction(fn () => $userList->delete());
    }
}
