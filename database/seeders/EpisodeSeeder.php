<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use AnimeSite\Enums\Kind;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Anime;

class EpisodeSeeder extends Seeder
{
    public function run(): void
    {
        $animes = Anime::all();

        foreach ($animes as $anime) {
            if ($anime->kind === Kind::TV_SERIES) {
                // Для фільмів типу Anime завжди один епізод з номером 1
                Episode::factory()
                    ->foranime($anime)
                    ->create(['number' => 1]);
            } elseif ($anime->kind === Kind::TV_SERIES) {
                $episodeCount = rand(2, 10);

                for ($i = 1; $i <= $episodeCount; $i++) {
                    $number = Episode::factory()->generateUniqueNumber($anime->id, false);

                    Episode::factory()
                        ->forAnime($anime)
                        ->create(['number' => $number]);
                }
            }
        }
    }
}
