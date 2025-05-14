<?php

namespace AnimeSite\Actions\UserLists;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserList;

class ShowUserList
{
    public function __invoke(UserList $userList): UserList
    {
        Gate::authorize('view', $userList);

        return $userList->loadMissing(['listable']);
    }
}
