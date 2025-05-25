<?php

namespace AnimeSite\Actions\UserSubscriptions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserSubscription;

class RenewSubscription
{
    /**
     * Увімкнути автоматичне продовження підписки.
     *
     * @param UserSubscription $subscription
     * @return UserSubscription
     */
    public function __invoke(UserSubscription $subscription): UserSubscription
    {
        // Перевіряємо права доступу
        Gate::authorize('update', $subscription);

        return DB::transaction(function () use ($subscription) {
            // Вмикаємо автоматичне продовження
            $subscription->update([
                'auto_renew' => true,
            ]);
            
            // Завантажуємо зв'язані дані
            return $subscription->loadMissing(['user', 'tariff']);
        });
    }
}
