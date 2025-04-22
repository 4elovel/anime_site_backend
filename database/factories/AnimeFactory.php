<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Liamtseva\Cinema\Enums\ApiSourceName;
use Liamtseva\Cinema\Enums\AttachmentType;
use Liamtseva\Cinema\Enums\Country;
use Liamtseva\Cinema\Enums\Kind;
use Liamtseva\Cinema\Enums\AnimeRelateType;
use Liamtseva\Cinema\Enums\Period;
use Liamtseva\Cinema\Enums\RestrictedRating;
use Liamtseva\Cinema\Enums\Source;
use Liamtseva\Cinema\Enums\Status;
use Liamtseva\Cinema\Models\Anime;
use Liamtseva\Cinema\Models\Studio;
use Liamtseva\Cinema\ValueObjects\ApiSource;
use Liamtseva\Cinema\ValueObjects\Attachment;
use Liamtseva\Cinema\ValueObjects\AnimeRelate;

// Баг
class AnimeFactory extends Factory
{
    protected $model = Anime::class;

    public function definition(): array
    {
        $animeOrTv =  'anime';
        $animeData = $this->getAnimeData($animeOrTv);

        $titleKey =  'title';
        $title = $animeData[$titleKey] ?? $this->faker->word;
        $originalTitle = $animeData["original_{$titleKey}"] ?? $this->faker->words(rand(0, 10));
        $kind = Kind::TV_SERIES;

        $releaseDate = $animeData['release_date'] ?? $animeData['first_air_date'] ?? null;
        $period = $releaseDate ? Period::fromDate($releaseDate) : null;

        $firstAirDate = $this->parseValidDate($releaseDate)
            ?? $this->parseValidDate($releaseDate)
            ?? $this->faker->date();

        $lastAirDate = $this->parseValidDate($releaseDate)
            ?? $this->parseValidDate($releaseDate)
            ?? $this->faker->date();

        return [
            'api_sources' => $this->getApiSources($animeData),
            'slug' => $title,
            'name' => $title,
            'description' => $this->getDescription($animeData),
            'image_name' => $this->faker->imageUrl(200, 100, 'anime'),
            'aliases' => collect([$originalTitle]),
            'studio_id' => Studio::query()->inRandomOrder()->value('id') ?? Studio::factory(),
            'kind' => $kind->value,
            'status' => $this->determineStatus($animeData)->value,
            'period' => $period?->value,
            'restricted_rating' => $this->faker->randomElement(RestrictedRating::cases())->value,
            'source' => $this->faker->randomElement(Source::cases())->value,
            'countries' => $this->getCountries($animeData),
            'poster' => $this->getPoster($animeData),
            'duration' => $this->getDuration($animeData),
            'episodes_count' => $this->getEpisodesCount($animeData),
            'first_air_date' => $firstAirDate,
            'last_air_date' => $lastAirDate,
            'imdb_score' => $animeData['vote_average'] ?? $this->faker->randomFloat(1, 1, 10),
            'attachments' => $this->generateAttachments(),
            'related' => $this->generateRelatedAnimes(),
            'similars' => [],
            'is_published' => $this->faker->boolean(),
            'meta_title' => "Дивитись онлайн $title | ".config('app.name'),
            'meta_description' => $this->getDescription($animeData),
            'meta_image' => $this->getBackdrop($animeData),
        ];
    }

    /**
     * Отримуємо дані фільму або серіалу з TMDB API
     */
    private function getAnimeData(string $animeOrTv): array
    {
        $randomId = $this->faker->numberBetween(70_000, 90_000);

        $response = Http::get("https://api.themoviedb.org/3/{$animeOrTv}/{$randomId}", [
            'api_key' => env('TMDB_API_KEY'),
            'language' => 'uk-UA',
            'append_to_response' => 'tags, images, videos, production_countries',
        ]);

        return $response->successful() ? $response->json() : [];
    }

    public function parseValidDate($date)
    {
        return ! empty($date) && Carbon::parse($date)->isValid() ? Carbon::parse($date)->toDateString() : null;
    }

    public function getApiSources(array $animeData)
    {
        return collect([
            'id' => ApiSourceName::TMDB,
            'imdb_id' => ApiSourceName::IMDB,
        ])
            ->filter(fn ($source, $key) => isset($animeData[$key]))
            ->map(fn ($source, $key) => new ApiSource($source, $animeData[$key]))
            ->values()
            ->toArray();
    }

    public function getDescription(array $animeData): string
    {

        return data_get($animeData, 'overview', $this->faker->sentence(15));
    }

    /**
     * Визначаємо статус залежно від доступних даних
     */
    public function determineStatus(array $animeData): Status
    {
        if (isset($animeData['status'])) {
            if ($animeData['status'] === 'Ended' || $animeData['status'] === 'Released') {
                return Status::RELEASED;
            }

            if ($animeData['status'] === 'Canceled') {
                return Status::CANCELED;
            }

            if ($animeData['in_production'] === true) {
                return Status::ONGOING;
            }

            return Status::from($animeData['status']);
        }

        return $this->faker->randomElement(Status::cases());
    }

    /**
     * Отримуємо країни
     * TODO: не робить, виправити, лише USA ставить :)
     */
    public function getCountries(array $animeData): Collection
    {
        $countries = $animeData['production_countries'] ?? [];

        return collect($countries)->map(function ($country) {
            return Country::tryFrom($country['iso_3166_1']) ?? Country::USA;
        });
    }

    /**
     * Отримуємо постер
     */
    public function getPoster(array $animeData): string
    {
        return isset($animeData['poster_path']) ? "https://image.tmdb.org/t/p/w500{$animeData['poster_path']}" : $this->faker->imageUrl(800, 1200);
    }

    /**
     * Отримуємо тривалість фільму або серіалу
     */
    private function getDuration(array $animeData): int
    {
        return $animeData['runtime'] ?? $this->faker->numberBetween(60, 180);
    }

    /**
     * Отримуємо кількість епізодів
     */
    public function getEpisodesCount(array $animeData): int
    {
        return $animeData['episode_count'] ?? 1;
    }

    /**
     * Генеруємо прикріплені файли
     */
    public function generateAttachments(): Collection
    {
        return collect([
            new Attachment(AttachmentType::PICTURE, $this->faker->imageUrl()),
            new Attachment(AttachmentType::TRAILER, $this->faker->url()),
            new Attachment(AttachmentType::CLIP, $this->faker->url()),
        ]);
    }

    /**
     * Генеруємо зв'язки з іншими фільмами (наприклад, сезони, приквели, сиквели)
     */
    private function generateRelatedAnimes(): Collection
    {
        return collect([
            new AnimeRelate($this->faker->randomNumber(), AnimeRelateType::SEASON),
            new AnimeRelate($this->faker->randomNumber(), AnimeRelateType::SEQUEL),
            new AnimeRelate($this->faker->randomNumber(), AnimeRelateType::PREQUEL),
        ]);
    }

    /**
     * Отримуємо задник
     */
    public function getBackdrop(array $animeData): string
    {
        return isset($animeData['backdrop_path']) ? "https://image.tmdb.org/t/p/w500{$animeData['backdrop_path']}" : $this->faker->imageUrl(800, 1200);
    }
}
