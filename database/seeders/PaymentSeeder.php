<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use AnimeSite\Enums\PaymentStatus;
use AnimeSite\Models\Payment;
use AnimeSite\Models\Tariff;
use AnimeSite\Models\User;
use AnimeSite\Models\UserSubscription;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Get all active subscriptions
        $subscriptions = UserSubscription::all();

        if ($subscriptions->isEmpty()) {
            $this->command->info('No subscriptions found. Please run UserSubscriptionSeeder first.');
            return;
        }

        // Create successful payments for all active subscriptions
        foreach ($subscriptions as $subscription) {
            Payment::factory()
                ->forUser(User::find($subscription->user_id))
                ->successful()
                ->create([
                    'tariff_id' => $subscription->tariff_id,
                    'amount' => Tariff::find($subscription->tariff_id)->price,
                    'currency' => Tariff::find($subscription->tariff_id)->currency,
                ]);
        }

        // Create some failed payments (10% of total subscriptions)
        $failedPaymentsCount = ceil($subscriptions->count() * 0.1);
        $users = User::inRandomOrder()->take($failedPaymentsCount)->get();
        $tariffs = Tariff::all();

        foreach ($users as $user) {
            Payment::factory()
                ->forUser($user)
                ->failed()
                ->create([
                    'tariff_id' => $tariffs->random()->id,
                ]);
        }

        // Create some pending payments (5% of total subscriptions)
        $pendingPaymentsCount = ceil($subscriptions->count() * 0.05);
        $users = User::inRandomOrder()->take($pendingPaymentsCount)->get();

        foreach ($users as $user) {
            Payment::factory()
                ->forUser($user)
                ->pending()
                ->create([
                    'tariff_id' => $tariffs->random()->id,
                ]);
        }

        // Create some refunded payments (3% of total subscriptions)
        $refundedPaymentsCount = ceil($subscriptions->count() * 0.03);
        $users = User::inRandomOrder()->take($refundedPaymentsCount)->get();

        foreach ($users as $user) {
            Payment::factory()
                ->forUser($user)
                ->refunded()
                ->create([
                    'tariff_id' => $tariffs->random()->id,
                ]);
        }
    }
}
