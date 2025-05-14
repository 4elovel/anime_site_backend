<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use AnimeSite\Models\Tariff;
use AnimeSite\Models\User;
use AnimeSite\Models\UserSubscription;

class UserSubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users and tariffs
        $users = User::all();
        $tariffs = Tariff::all();

        if ($tariffs->isEmpty()) {
            $this->command->info('No tariffs found. Please run TariffSeeder first.');
            return;
        }

        // Create subscriptions for 40% of users
        $usersToSubscribe = $users->random(ceil($users->count() * 0.4));

        foreach ($usersToSubscribe as $user) {
            // Randomly select a tariff
            $tariff = $tariffs->random();

            // Create an active subscription
            UserSubscription::factory()
                ->forUser($user)
                ->active()
                ->create([
                    'tariff_id' => $tariff->id,
                    'auto_renew' => fake()->boolean(70), // 70% chance of auto-renew
                ]);
        }

        // Create expired subscriptions for 20% of users
        $usersWithExpiredSubscriptions = $users->diff($usersToSubscribe)->random(ceil($users->count() * 0.2));

        foreach ($usersWithExpiredSubscriptions as $user) {
            // Randomly select a tariff
            $tariff = $tariffs->random();

            // Create an expired subscription
            UserSubscription::factory()
                ->forUser($user)
                ->expired()
                ->create([
                    'tariff_id' => $tariff->id,
                ]);
        }
    }
}
