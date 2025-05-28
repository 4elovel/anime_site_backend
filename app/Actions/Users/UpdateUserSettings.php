<?php

namespace AnimeSite\Actions\Users;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;

class UpdateUserSettings
{
    /**
     * Оновити налаштування користувача.
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    public function __invoke(User $user, array $data): array
    {
        Gate::authorize('update', $user);

        return DB::transaction(function () use ($user, $data) {
            $user->update($data);

            return [
                // User preferences
                'allow_adult' => $user->allow_adult,
                'is_auto_next' => $user->is_auto_next,
                'is_auto_play' => $user->is_auto_play,
                'is_auto_skip_intro' => $user->is_auto_skip_intro,
                'is_private_favorites' => $user->is_private_favorites,

                // Notification preferences - Episodes
                'notify_new_episodes' => $user->notify_new_episodes,
                'notify_episode_date_changes' => $user->notify_episode_date_changes,
                'notify_announcement_to_ongoing' => $user->notify_announcement_to_ongoing,

                // Notification preferences - Comments
                'notify_comment_replies' => $user->notify_comment_replies,
                'notify_comment_likes' => $user->notify_comment_likes,

                // Notification preferences - Ratings
                'notify_review_replies' => $user->notify_review_replies,

                // Notification preferences - UserList
                'notify_planned_reminders' => $user->notify_planned_reminders,

                // Notification preferences - Selections
                'notify_new_selections' => $user->notify_new_selections,

                // Notification preferences - Movies
                'notify_status_changes' => $user->notify_status_changes,
                'notify_new_seasons' => $user->notify_new_seasons,

                // Notification preferences - Subscription
                'notify_subscription_expiration' => $user->notify_subscription_expiration,
                'notify_subscription_renewal' => $user->notify_subscription_renewal,
                'notify_payment_issues' => $user->notify_payment_issues,
                'notify_tariff_changes' => $user->notify_tariff_changes,

                // Notification preferences - System
                'notify_site_updates' => $user->notify_site_updates,
                'notify_maintenance' => $user->notify_maintenance,
                'notify_security_changes' => $user->notify_security_changes,
                'notify_new_features' => $user->notify_new_features,
            ];
        });
    }
}
