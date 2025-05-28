<?php

namespace AnimeSite\Actions\Users;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;

class CreateUser
{
    /**
     * @param array{
     *     name: string,
     *     email: string,
     *     password: string,
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
    public function __invoke(array $data): User
    {
        Gate::authorize('create', User::class);

        return DB::transaction(function () use ($data) {
            // Create the user first
            $user = User::create($data);

            // Handle avatar upload
            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                $user->avatar = $user->handleFileUpload($data['avatar'], 'avatar_users');
            }

            // Handle backdrop upload
            if (isset($data['backdrop']) && $data['backdrop'] instanceof UploadedFile) {
                $user->backdrop = $user->handleFileUpload($data['backdrop'], 'backdrop_users');
            }

            // Save the changes
            $user->save();

            return $user;
        });
    }
}
