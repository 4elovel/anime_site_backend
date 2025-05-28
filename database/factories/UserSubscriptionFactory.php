<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AnimeSite\Models\Tariff;
use AnimeSite\Models\User;
use AnimeSite\Models\UserSubscription;

/**
 * @extends Factory<UserSubscription>
 */
class UserSubscriptionFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $tariff = Tariff::inRandomOrder()->first() ?? Tariff::factory()->create();
        $endDate = (clone $startDate)->modify("+{$tariff->duration_days} days");
        
        return [
            'user_id' => User::factory(),
            'tariff_id' => $tariff->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => true,
            'auto_renew' => $this->faker->boolean(70),
        ];
    }

    /**
     * Indicate that the subscription is active and not expired
     */
    public function active(): self
    {
        return $this->state(function (array $attributes) {
            $startDate = now()->subDays($this->faker->numberBetween(1, 20));
            $tariff = Tariff::find($attributes['tariff_id']) ?? Tariff::factory()->create();
            $endDate = (clone $startDate)->modify("+{$tariff->duration_days} days");
            
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
            ];
        });
    }

    /**
     * Indicate that the subscription is expired
     */
    public function expired(): self
    {
        return $this->state(function (array $attributes) {
            $endDate = now()->subDays($this->faker->numberBetween(1, 30));
            $tariff = Tariff::find($attributes['tariff_id']) ?? Tariff::factory()->create();
            $startDate = (clone $endDate)->modify("-{$tariff->duration_days} days");
            
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => false,
            ];
        });
    }

    /**
     * Indicate that the subscription has auto-renew enabled
     */
    public function withAutoRenew(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'auto_renew' => true,
            ];
        });
    }

    /**
     * Create a subscription for a specific user
     */
    public function forUser(User $user): self
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }
}
