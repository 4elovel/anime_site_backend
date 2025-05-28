<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use AnimeSite\Models\Achievement;
use AnimeSite\Models\AchievementUser;
use AnimeSite\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AnimeSite\Models\AchievementUser>
 */
class AchievementUserFactory extends Factory
{
    protected $model = AchievementUser::class;

    public function definition()
    {
        return [
            'id' => Str::ulid(),
            'user_id' => User::factory(),
            'achievement_id' => Achievement::factory(),
            'progress_count' => $this->faker->numberBetween(0, 100),
        ];
    }
}
