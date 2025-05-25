<?php

namespace AnimeSite\Actions\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPassword
{
    /**
     * Скидання пароля користувача.
     *
     * @param array{
     *     token: string,
     *     email: string,
     *     password: string
     * } $data
     * @return string
     */
    public function __invoke(array $data): string
    {
        return DB::transaction(function () use ($data) {
            // Скидаємо пароль
            $status = Password::reset(
                [
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'password_confirmation' => $data['password'],
                    'token' => $data['token'],
                ],
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();
                    
                    event(new PasswordReset($user));
                }
            );
            
            if ($status !== Password::PASSWORD_RESET) {
                throw new \Exception(__($status));
            }
            
            return $status;
        });
    }
}
