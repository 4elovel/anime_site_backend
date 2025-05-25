<?php

namespace AnimeSite\Actions\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use AnimeSite\Enums\Role;
use AnimeSite\Models\User;

class RegisterUser
{
    /**
     * Реєстрація нового користувача.
     *
     * @param array{
     *     name: string,
     *     email: string,
     *     password: string,
     *     password_confirmation: string,
     *     gender?: string|null,
     *     birthday?: string|null,
     *     description?: string|null,
     *     allow_adult?: bool,
     *     is_auto_next?: bool,
     *     is_auto_play?: bool,
     *     is_auto_skip_intro?: bool,
     *     is_private_favorites?: bool
     * } $data
     * @return array{user: User, token: string}
     */
    public function __invoke(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Перевіряємо, що паролі співпадають
            if (isset($data['password_confirmation']) && $data['password'] !== $data['password_confirmation']) {
                throw new \InvalidArgumentException('Підтвердження пароля не співпадає з паролем.');
            }

            // Хешуємо пароль
            $data['password'] = Hash::make($data['password']);

            // Встановлюємо роль за замовчуванням
            $data['role'] = Role::USER;

            // Видаляємо поле підтвердження пароля перед створенням користувача
            if (isset($data['password_confirmation'])) {
                unset($data['password_confirmation']);
            }

            // Створюємо користувача
            $user = User::create($data);

            // Створюємо токен для API
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }
}
