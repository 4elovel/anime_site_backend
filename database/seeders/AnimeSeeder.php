<?php

namespace Database\Seeders;

use Database\Factories\AnimeFactory;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use AnimeSite\Enums\Kind;
use AnimeSite\Enums\Period;
use AnimeSite\Enums\RestrictedRating;
use AnimeSite\Enums\Source;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Person;
use AnimeSite\Models\Studio;
use AnimeSite\Models\Tag;
use AnimeSite\Models\User;

class AnimeSeeder extends Seeder
{
    public function run(): void
    {

        Anime::factory(20)->create();


        /*// Отримуємо топ 100 фільмів/серіалів з TheanimeDB
        $animesData = $this->getTopanimesFromTMDB();

        $releaseDate = $animeData['release_date'] ?? $animeData['first_air_date'] ?? null;
        $period = $releaseDate ? Period::fromDate($releaseDate) : null;

        $firstAirDate = $animeFactory->parseValidDate($releaseDate)
            ?? $animeFactory->parseValidDate($releaseDate)
            ?? $faker->date();

        $lastAirDate = $animeFactory->parseValidDate($releaseDate)
            ?? $animeFactory->parseValidDate($releaseDate)
            ?? $faker->date();

        // Прив'язуємо фільми до користувачів
        $animes = collect($animesData)->map(function ($animeData) use ($period, $animeFactory, $faker, $firstAirDate, $lastAirDate) {

            // Створюємо фільм
            return Anime::create([
                'api_sources' => $animeFactory->getApiSources($animeData),
                'slug' => $animeData['title'],
                'name' => $animeData['title'],
                'description' => $animeFactory->getDescription($animeData),
                'image_name' => $faker->imageUrl(200, 100, 'animes'),
                'aliases' => collect([$animeData['original_title'] ?? $faker->words(rand(0, 10))]),
                'studio_id' => Studio::query()->inRandomOrder()->value('id'),
                'kind' => Kind::ANIME,
                'status' => $animeFactory->determineStatus($animeData)->value,
                'period' => $period?->value,
                'restricted_rating' => $faker->randomElement(RestrictedRating::cases())->value,
                'source' => $faker->randomElement(Source::cases())->value,
                'countries' => $animeFactory->getCountries($animeData),
                'poster' => $animeFactory->getPoster($animeData),
                'duration' => $animeData['runtime'] ?? 120,
                'episodes_count' => $animeFactory->getEpisodesCount($animeData),
                'first_air_date' => $firstAirDate,
                'last_air_date' => $lastAirDate,
                'imdb_score' => $animeData['vote_average'] ?? $faker->randomFloat(1, 1, 10),
                'attachments' => $animeFactory->generateAttachments(),
                'related' => [],
                'similars' => [],
                'is_published' => $faker->boolean(),
                'meta_title' => 'Дивитись онлайн '.$animeData['title'].' | '.config('app.name'),
                'meta_description' => $animeFactory->getDescription($animeData),
                'meta_image' => $animeFactory->getBackdrop($animeData),
            ]);
        });

        // Прив'язуємо фільми до користувачів
        User::all()->each(function ($user) use ($animes) {
            $user->animeNotifications()->attach(
                $animes->random(rand(1, 5))->pluck('id'),
                ['created_at' => now(), 'updated_at' => now()]
            );
        });

        // Отримуємо людей та теги
        $persons = Person::all();
        $tags = Tag::all();

        // Додаємо теги та людей до кожного фільму
        $animes->each(function ($anime) use ($persons, $tags) {
            // Додаємо від 5 до 20 випадкових тегів
            $randomTags = $tags->random(rand(5, 20));
            $anime->tags()->attach($randomTags->pluck('id'));

            // Додаємо випадкових людей (акторів)
            $randomPersons = $persons->random(rand(1, 5));
            foreach ($randomPersons as $person) {
                $anime->persons()->attach($person, [
                    'character_name' => fake()->name(),
                ]);
            }
        });
    }

    private function getTopAnimesFromTMDB(): array
    {
        // Отримуємо топ 100 фільмів/серіалів
        $response = Http::get('https://api.themoviedb.org/3/discover/anime', [
            'api_key' => env('TMDB_API_KEY'),
            'language' => 'uk-UA',
            'sort_by' => 'popularity.desc', // Сортуємо за популярністю
            'page' => 1, // Потрібно отримати першу сторінку
        ]);

        return $response->successful() ? $response->json()['results'] : [];*/
    }
}
