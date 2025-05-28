<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AnimeSite\Models\Episode;
use AnimeSite\Models\User;
use AnimeSite\Models\WatchHistory;

/**
 * @extends Factory<WatchHistory>
 */
class WatchHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Генеруємо користувача через фабрику
            'episode_id' => Episode::factory(), // Генеруємо епізод через фабрику
            'progress_time' => $this->faker->numberBetween(0, 3600), // Прогрес часу (до 1 години)
        ];
    }

    public function forUserWithCount(int $count, $user = null): self
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user ? $user->id : $attributes['user_id'],
            ];
        })->count($count); // Генеруємо кілька записів для одного користувача
    }
}
