<?php

namespace AnimeSite\Actions\Users;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;

class DeleteUser
{
    public function __invoke(User $user): void
    {
        Gate::authorize('delete', $user);

        DB::transaction(fn () => $user->delete());
    }
}
