<?php

namespace AnimeSite\Actions\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use AnimeSite\Models\User;

class LogoutUser
{
    /**
     * Вихід користувача.
     *
     * @param User $user
     * @return void
     */
    public function __invoke(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Видаляємо поточний токен
            $user->currentAccessToken()->delete();
        });
    }
}
