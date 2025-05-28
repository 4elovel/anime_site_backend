<?php

namespace AnimeSite\Actions\UserSubscriptions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserSubscription;

class ExtendSubscription
{
    /**
     * Продовжити підписку користувача на вказану кількість днів.
     *
     * @param UserSubscription $subscription
     * @param array{
     *     days: int,
     *     payment_id?: string|null
     * } $data
     * @return UserSubscription
     */
    public function __invoke(UserSubscription $subscription, array $data): UserSubscription
    {
        // Перевіряємо права доступу
        Gate::authorize('update', $subscription);

        return DB::transaction(function () use ($subscription, $data) {
            // Визначаємо нову дату закінчення
            $newEndDate = $subscription->end_date;
            
            // Якщо підписка вже закінчилася, починаємо з поточної дати
            if ($subscription->isExpired()) {
                $newEndDate = now();
            }
            
            // Додаємо вказану кількість днів
            $newEndDate = $newEndDate->addDays($data['days']);
            
            // Оновлюємо підписку
            $subscription->update([
                'end_date' => $newEndDate,
                'is_active' => true,
                'payment_id' => $data['payment_id'] ?? $subscription->payment_id,
            ]);
            
            // Завантажуємо зв'язані дані
            return $subscription->loadMissing(['user', 'tariff']);
        });
    }
}
