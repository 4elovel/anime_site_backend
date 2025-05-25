<?php

namespace AnimeSite\Actions\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class ForgotPassword
{
    /**
     * Відправка посилання для відновлення пароля.
     *
     * @param array{email: string} $data
     * @return string
     */
    public function __invoke(array $data): string
    {
        return DB::transaction(function () use ($data) {
            // Відправляємо посилання для відновлення пароля
            $status = Password::sendResetLink(['email' => $data['email']]);
            
            if ($status !== Password::RESET_LINK_SENT) {
                throw new \Exception(__($status));
            }
            
            return $status;
        });
    }
}
