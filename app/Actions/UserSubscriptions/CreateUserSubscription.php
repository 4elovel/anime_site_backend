<?php

namespace AnimeSite\Actions\UserSubscriptions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserSubscription;

class CreateUserSubscription
{
    /**
     * Створити нову підписку користувача.
     *
     * @param array{
     *     user_id: string,
     *     tariff_id: string,
     *     payment_id?: string|null,
     *     start_date: string|\DateTimeInterface,
     *     end_date: string|\DateTimeInterface,
     *     is_active?: bool,
     *     auto_renew?: bool
     * } $data
     * @return UserSubscription
     */
    public function __invoke(array $data): UserSubscription
    {
        // Перевіряємо права доступу
        Gate::authorize('create', UserSubscription::class);

        return DB::transaction(function () use ($data) {
            // Встановлюємо значення за замовчуванням
            $data['is_active'] = $data['is_active'] ?? true;
            $data['auto_renew'] = $data['auto_renew'] ?? false;
            
            // Створюємо підписку
            $subscription = UserSubscription::create($data);
            
            // Завантажуємо зв'язані дані
            return $subscription->loadMissing(['user', 'tariff']);
        });
    }
}
