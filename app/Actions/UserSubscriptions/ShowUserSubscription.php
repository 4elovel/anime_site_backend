<?php

namespace AnimeSite\Actions\UserSubscriptions;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserSubscription;

class ShowUserSubscription
{
    /**
     * Отримати конкретну підписку користувача.
     *
     * @param UserSubscription $subscription
     * @return UserSubscription
     */
    public function __invoke(UserSubscription $subscription): UserSubscription
    {
        // Перевіряємо права доступу
        Gate::authorize('view', $subscription);
        
        // Завантажуємо зв'язані дані
        return $subscription->loadMissing(['user', 'tariff']);
    }
}
