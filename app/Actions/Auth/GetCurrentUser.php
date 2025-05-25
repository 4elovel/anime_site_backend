<?php

namespace AnimeSite\Actions\Auth;

use AnimeSite\Models\User;

class GetCurrentUser
{
    /**
     * Отримання поточного користувача.
     *
     * @param User $user
     * @return User
     */
    public function __invoke(User $user): User
    {
        return $user->loadMissing(['ratings', 'comments', 'watchHistories']);
    }
}
