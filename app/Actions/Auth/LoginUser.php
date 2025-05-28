<?php

namespace AnimeSite\Actions\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use AnimeSite\Models\User;

class LoginUser
{
    /**
     * Вхід користувача.
     *
     * @param array{
     *     email: string,
     *     password: string,
     *     remember?: bool
     * } $data
     * @return array{user: User, token: string}
     * @throws ValidationException
     */
    public function __invoke(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Спроба автентифікації
            if (!Auth::attempt([
                'email' => $data['email'],
                'password' => $data['password'],
            ], $data['remember'] ?? false)) {
                throw ValidationException::withMessages([
                    'email' => ['Невірні облікові дані.'],
                ]);
            }
            
            // Отримуємо користувача
            $user = User::where('email', $data['email'])->first();
            
            // Видаляємо старі токени
            $user->tokens()->delete();
            
            // Створюємо новий токен
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }
}
