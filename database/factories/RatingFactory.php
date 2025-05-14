<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Rating;
use AnimeSite\Models\User;

/**
 * @extends Factory<Rating>
 */
class RatingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'anime_id' => Anime::factory(),
            'number' => $this->faker->numberBetween(1, 10),
            'review' => $this->faker->optional()->text(200),
        ];
    }
}
