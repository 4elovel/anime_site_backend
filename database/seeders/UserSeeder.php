<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use AnimeSite\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(20)->create();
        User::factory()->state(['email' => 'admin@gmail.com', 'password' => 'admin'])->admin()->create();
    }
}
