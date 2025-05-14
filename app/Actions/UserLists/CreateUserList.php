<?php

namespace AnimeSite\Actions\UserLists;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserList;

class CreateUserList
{
    /**
     * @param array{
     *     user_id: string,
     *     listable_type: string,
     *     listable_id: string,
     *     type: string
     * } $data
     */
    public function __invoke(array $data): UserList
    {
        Gate::authorize('create', UserList::class);

        return DB::transaction(fn () =>
        UserList::create($data)
        );
    }
}
