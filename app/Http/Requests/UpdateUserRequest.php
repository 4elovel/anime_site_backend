<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\Gender;
use AnimeSite\Enums\Role;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Авторизація буде перевірена в екшині через Gate
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:128',
            ],
            'email' => [
                'sometimes',
                'email',
                'max:128',
                Rule::unique('users')->ignore($this->user),
            ],
            'password' => [
                'sometimes',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => [
                'sometimes',
                Rule::enum(Role::class),
            ],
            'gender' => [
                'sometimes',
                'nullable',
                Rule::enum(Gender::class),
            ],
            'birthday' => [
                'sometimes',
                'nullable',
                'date',
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:512',
            ],
            'allow_adult' => [
                'sometimes',
                'boolean',
            ],
            'is_auto_next' => [
                'sometimes',
                'boolean',
            ],
            'is_auto_play' => [
                'sometimes',
                'boolean',
            ],
            'is_auto_skip_intro' => [
                'sometimes',
                'boolean',
            ],
            'is_private_favorites' => [
                'sometimes',
                'boolean',
            ],
            // Notification preferences - Episodes
            'notify_new_episodes' => [
                'sometimes',
                'boolean',
            ],
            'notify_episode_date_changes' => [
                'sometimes',
                'boolean',
            ],
            'notify_announcement_to_ongoing' => [
                'sometimes',
                'boolean',
            ],
            // Notification preferences - Comments
            'notify_comment_replies' => [
                'sometimes',
                'boolean',
            ],
            'notify_comment_likes' => [
                'sometimes',
                'boolean',
            ],
            // Notification preferences - Ratings
            'notify_review_replies' => [
                'sometimes',
                'boolean',
            ],
            // Notification preferences - UserList
            'notify_planned_reminders' => [
                'sometimes',
                'boolean',
            ],
            // Notification preferences - Selections
            'notify_new_selections' => [
                'sometimes',
                'boolean',
            ],
            // Notification preferences - Movies
            'notify_status_changes' => [
                'sometimes',
                'boolean',
            ],
            'notify_new_seasons' => [
                'sometimes',
                'boolean',
            ],
            // Notification preferences - Subscription
            'notify_subscription_expiration' => [
                'sometimes',
                'boolean',
            ],
            'notify_subscription_renewal' => [
                'sometimes',
                'boolean',
            ],
            'notify_payment_issues' => [
                'sometimes',
                'boolean',
            ],
            'notify_tariff_changes' => [
                'sometimes',
                'boolean',
            ],
            // Notification preferences - System
            'notify_site_updates' => [
                'sometimes',
                'boolean',
            ],
            'notify_maintenance' => [
                'sometimes',
                'boolean',
            ],
            'notify_security_changes' => [
                'sometimes',
                'boolean',
            ],
            'notify_new_features' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Ім\'я має бути рядком.',
            'name.max' => 'Ім\'я не може перевищувати 128 символів.',
            'email.email' => 'Введіть коректну електронну адресу.',
            'email.max' => 'Електронна адреса не може перевищувати 128 символів.',
            'email.unique' => 'Ця електронна адреса вже використовується.',
            'password.min' => 'Пароль повинен містити щонайменше 8 символів.',
            'password.confirmed' => 'Підтвердження пароля не співпадає.',
            'role.enum' => 'Невірне значення для ролі.',
            'gender.enum' => 'Невірне значення для статі.',
            'birthday.date' => 'Дата народження повинна бути дійсною датою.',
            'description.string' => 'Опис має бути рядком.',
            'description.max' => 'Опис не може перевищувати 512 символів.',
        ];
    }
}
