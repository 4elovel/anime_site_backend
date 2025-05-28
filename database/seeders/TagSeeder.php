<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use AnimeSite\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        Tag::factory(1_000)->create();
    }
}
