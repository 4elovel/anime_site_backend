<?php

namespace AnimeSite\Actions\Users;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;

class UpdateUserProfile
{
    /**
     * Оновити профіль користувача.
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    public function __invoke(User $user, array $data): array
    {
        Gate::authorize('update', $user);

        return DB::transaction(function () use ($user, $data) {
            $user->update($data);

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
        });
    }
}
