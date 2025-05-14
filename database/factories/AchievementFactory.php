<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use AnimeSite\Enums\Gender;
use AnimeSite\Enums\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AnimeSite\Models\Achievement>
 */
class AchievementFactory extends Factory
{

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->userName(),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(10),
            'icon' => fake()->imageUrl(200, 200, 'abstract', true, 'achievement-icon'),
            'max_counts' => $this->faker->numberBetween(1, 100),
        ];
    }
}
