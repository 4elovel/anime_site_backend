<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserSettingsRequest extends FormRequest
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
            'allow_adult.boolean' => 'Значення має бути true або false.',
            'is_auto_next.boolean' => 'Значення має бути true або false.',
            'is_auto_play.boolean' => 'Значення має бути true або false.',
            'is_auto_skip_intro.boolean' => 'Значення має бути true або false.',
            'is_private_favorites.boolean' => 'Значення має бути true або false.',
        ];
    }
}
