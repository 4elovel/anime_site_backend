<?php

namespace AnimeSite\Actions\Users;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;

class UploadUserBackdrop
{
    /**
     * @param User $user
     * @param UploadedFile $backdrop
     * @return string|null
     */
    public function __invoke(User $user, UploadedFile $backdrop): ?string
    {
        Gate::authorize('update', $user);

        return DB::transaction(function () use ($user, $backdrop) {
            // Handle backdrop upload
            $user->backdrop = $user->handleFileUpload($backdrop, 'backdrop_users', $user->backdrop);
            $user->save();

            return $user->getFileUrl($user->backdrop);
        });
    }
}
