<?php

namespace AnimeSite\Actions\Users;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;

class GetUserProfile
{
    /**
     * Отримати профіль користувача.
     *
     * @param User $user
     * @return array
     */
    public function __invoke(User $user): array
    {
        Gate::authorize('view', $user);

        return [
            'name' => $user->name,
            'email' => $user->email,
            'gender' => $user->gender,
            'birthday' => $user->birthday,
            'description' => $user->description,
            'avatar' => $user->avatar ? $user->getFileUrl($user->avatar) : null,
            'backdrop' => $user->backdrop ? $user->getFileUrl($user->backdrop) : null,
            'created_at' => $user->created_at,
        ];
    }
}
