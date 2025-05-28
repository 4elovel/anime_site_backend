<?php

namespace AnimeSite\Actions\Users;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;

class UpdateUser
{
    /**
     * @param User $user
     * @param array{
     *     name?: string,
     *     email?: string,
     *     password?: string,
     *     role?: string,
     *     avatar?: string|UploadedFile|null,
     *     backdrop?: string|UploadedFile|null,
     *     gender?: string|null,
     *     birthday?: string|null,
     *     description?: string|null,
     *     allow_adult?: bool,
     *     is_auto_next?: bool,
     *     is_auto_play?: bool,
     *     is_auto_skip_intro?: bool,
     *     is_private_favorites?: bool
     * } $data
     */
    public function __invoke(User $user, array $data): User
    {
        Gate::authorize('update', $user);

        return DB::transaction(function () use ($user, $data) {
            // Handle avatar upload
            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                $data['avatar'] = $user->handleFileUpload($data['avatar'], 'avatar_users', $user->avatar);
            }

            // Handle backdrop upload
            if (isset($data['backdrop']) && $data['backdrop'] instanceof UploadedFile) {
                $data['backdrop'] = $user->handleFileUpload($data['backdrop'], 'backdrop_users', $user->backdrop);
            }

            // Update the user
            $user->update($data);

            return $user;
        });
    }
}
