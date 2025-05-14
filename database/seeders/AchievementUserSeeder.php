<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use AnimeSite\Models\Achievement;
use AnimeSite\Models\AchievementUser;

class AchievementUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AchievementUser::factory()->count(10)->create();
    }
}
