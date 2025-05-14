<?php

namespace AnimeSite\Actions\Users;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;

class UploadUserAvatar
{
    /**
     * @param User $user
     * @param UploadedFile $avatar
     * @return string|null
     */
    public function __invoke(User $user, UploadedFile $avatar): ?string
    {
        Gate::authorize('update', $user);

        return DB::transaction(function () use ($user, $avatar) {
            // Handle avatar upload
            $user->avatar = $user->handleFileUpload($avatar, 'avatar_users', $user->avatar);
            $user->save();

            return $user->getFileUrl($user->avatar);
        });
    }
}
