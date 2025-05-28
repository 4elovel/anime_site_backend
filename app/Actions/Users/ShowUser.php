<?php

namespace AnimeSite\Actions\Users;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;

class ShowUser
{
    public function __invoke(User $user): User
    {
        Gate::authorize('view', $user);
        return $user->loadMissing(['ratings', 'comments', 'watchHistories']);
    }
}
