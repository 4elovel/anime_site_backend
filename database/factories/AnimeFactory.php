<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use AnimeSite\Enums\ApiSourceName;
use AnimeSite\Enums\AttachmentType;
use AnimeSite\Enums\Country;
use AnimeSite\Enums\Kind;
use AnimeSite\Enums\AnimeRelateType;
use AnimeSite\Enums\Period;
use AnimeSite\Enums\RestrictedRating;
use AnimeSite\Enums\Source;
use AnimeSite\Enums\Status;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Studio;
use AnimeSite\ValueObjects\ApiSource;
use AnimeSite\ValueObjects\Attachment;
use AnimeSite\ValueObjects\AnimeRelate;

class AnimeFactory extends Factory
{
    protected $model = Anime::class;

    public function definition()
    {
        $name = $this->faker->sentence(3);
        $studio = Studio::query()->inRandomOrder()->first() ?? Studio::factory()->create();

        return [
            'id' => Str::ulid(),
            'slug' => Str::slug($name),
            'name' => $name,
            'description' => $this->faker->paragraph(),
            'image_name' => $this->faker->imageUrl(640, 480, 'anime', true, 'Anime Image'),
            'aliases' => json_encode($this->faker->words(3)), // JSON encoded array of strings
            'api_sources' => json_encode($this->generateApiSources()), // JSON encoded array of ApiSource objects
            'studio_id' => $studio->id,
            'countries' => json_encode($this->generateCountries()), // JSON encoded array of country codes
            'poster' => $this->faker->imageUrl(300, 450, 'anime', true, 'Poster'), // Vertical poster
            'duration' => $this->faker->numberBetween(20, 120), // Duration in minutes
            'episodes_count' => $this->faker->numberBetween(1, 100),
            'first_air_date' => $this->faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'last_air_date' => $this->faker->boolean() ? $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d') : null,
            'imdb_score' => $this->faker->randomFloat(2, 1, 10), // Rating from 1 to 10
            'attachments' => json_encode($this->generateAttachments()), // JSON encoded array of Attachment objects
            'related' => json_encode($this->generateRelated()), // JSON encoded array of AnimeRelate objects
            'similars' => json_encode($this->generateSimilars()), // JSON encoded array of anime IDs
            'is_published' => $this->faker->boolean(80), // 80% chance to be published
            'meta_title' => $this->faker->sentence(6),
            'meta_description' => $this->faker->text(150),
            'meta_image' => $this->faker->imageUrl(1200, 630, 'seo', true, 'SEO Image'),
            'kind' => $this->faker->randomElement(Kind::cases()),
            'status' => $this->faker->randomElement(Status::cases()),
            'period' => $this->faker->randomElement(array_merge(Period::cases(), [null])),
            'restricted_rating' => $this->faker->randomElement(RestrictedRating::cases()),
            'source' => $this->faker->randomElement(Source::cases()),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Generates an array of ApiSource objects.
     */
    private function generateApiSources(): array
    {
        $sources = $this->faker->randomElements(ApiSourceName::cases(), $this->faker->numberBetween(1, 3));
        return array_map(fn($source) => new ApiSource(
            name: $source,
            id: $this->faker->uuid()
        ), $sources);
    }

    /**
     * Generates an array of country codes.
     */
    private function generateCountries(): array
    {
        return $this->faker->randomElements(
            ['UA', 'US', 'GB', 'JP', 'FR', 'DE', 'KR'],
            $this->faker->numberBetween(1, 3)
        );
    }

    /**
     * Generates an array of Attachment objects.
     */
    private function generateAttachments(): array
    {
        $attachments = [];
        $count = $this->faker->numberBetween(0, 3);

        for ($i = 0; $i < $count; $i++) {
            $attachments[] = new Attachment(
                type: $this->faker->randomElement(AttachmentType::cases()),
                src: $this->faker->url(),
                title: $this->faker->sentence(4),
                duration: $this->faker->numberBetween(30, 300) // Seconds
            );
        }

        return $attachments;
    }

    /**
     * Generates an array of AnimeRelate objects.
     */
    private function generateRelated(): array
    {
        $related = [];
        $count = $this->faker->numberBetween(0, 2);

        for ($i = 0; $i < $count; $i++) {
            $related[] = new AnimeRelate(
                anime_id: Str::ulid(), // Unique ULID
                type: $this->faker->randomElement(AnimeRelateType::cases())
            );
        }

        return $related;
    }

    /**
     * Generates an array of anime IDs for similars.
     */
    private function generateSimilars(): array
    {
        $similars = [];
        $count = $this->faker->numberBetween(0, 4);

        for ($i = 0; $i < $count; $i++) {
            $similars[] = Str::ulid(); // Unique ULID
        }

        return $similars;
    }

    /**
     * Assigns a specific studio to the anime.
     */
    public function forStudio(Studio $studio): self
    {
        return $this->state(fn() => [
            'studio_id' => $studio->id,
        ]);
    }

    /**
     * Sets a specific anime kind.
     */
    public function withKind(Kind $kind): self
    {
        return $this->state(fn() => [
            'kind' => $kind,
        ]);
    }

    /**
     * Sets a specific anime status.
     */
    public function withStatus(Status $status): self
    {
        return $this->state(fn() => [
            'status' => $status,
        ]);
    }

    /**
     * Sets published status.
     */
    public function published(): self
    {
        return $this->state(fn() => [
            'is_published' => true,
        ]);
    }
}
