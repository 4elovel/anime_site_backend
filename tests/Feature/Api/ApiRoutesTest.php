<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use AnimeSite\Models\User;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Person;
use AnimeSite\Models\Studio;
use AnimeSite\Models\Tag;
use AnimeSite\Models\Selection;
use AnimeSite\Models\Tariff;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест для перевірки маршруту аніме
     */
    public function test_animes_route_works_for_unauthorized_users(): void
    {
        // Створюємо аніме
        Anime::factory()->count(3)->create();

        // Перевіряємо маршрут
        $response = $this->getJson('/api/v1/animes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'description',
                        'poster',
                        'meta',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /**
     * Тест для перевірки маршруту епізодів
     */
    public function test_episodes_route_works_for_unauthorized_users(): void
    {
        // Створюємо аніме та епізоди
        $anime = Anime::factory()->create();
        Episode::factory()->count(3)->create(['anime_id' => $anime->id]);

        // Перевіряємо маршрут
        $response = $this->getJson('/api/v1/episodes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'description',
                        'number',
                        'duration',
                        'air_date',
                        'anime_id',
                        'meta',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /**
     * Тест для перевірки маршруту останніх епізодів
     */
    public function test_latest_episodes_route_works_for_unauthorized_users(): void
    {
        // Створюємо аніме та епізоди
        $anime = Anime::factory()->create();
        Episode::factory()->count(3)->create(['anime_id' => $anime->id]);

        // Перевіряємо маршрут
        $response = $this->getJson('/api/v1/episodes/latest');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'description',
                        'number',
                        'duration',
                        'air_date',
                        'anime_id',
                        'meta',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /**
     * Тест для перевірки маршруту тегів
     */
    public function test_tags_route_works_for_unauthorized_users(): void
    {
        // Створюємо теги
        Tag::factory()->count(3)->create();

        // Перевіряємо маршрут
        $response = $this->getJson('/api/v1/tags');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'description',
                        'is_genre',
                        'meta',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /**
     * Тест для перевірки маршруту студій
     */
    public function test_studios_route_works_for_unauthorized_users(): void
    {
        // Створюємо студії
        Studio::factory()->count(3)->create();

        // Перевіряємо маршрут
        $response = $this->getJson('/api/v1/studios');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'description',
                        'meta',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /**
     * Тест для перевірки маршруту персон
     */
    public function test_people_route_works_for_unauthorized_users(): void
    {
        // Створюємо персони
        Person::factory()->count(3)->create();

        // Перевіряємо маршрут
        $response = $this->getJson('/api/v1/people');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'description',
                        'meta',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /**
     * Тест для перевірки маршруту добірок
     */
    public function test_selections_route_works_for_unauthorized_users(): void
    {
        // Створюємо добірки
        Selection::factory()->count(3)->create();

        // Перевіряємо маршрут
        $response = $this->getJson('/api/v1/selections');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'description',
                        'meta',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /**
     * Тест для перевірки маршруту тарифів
     */
    public function test_tariffs_route_works_for_unauthorized_users(): void
    {
        // Створюємо тарифи
        Tariff::factory()->count(3)->create();

        // Перевіряємо маршрут
        $response = $this->getJson('/api/v1/tariffs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'description',
                        'price',
                        'currency',
                        'meta',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /**
     * Тест для перевірки маршруту пошуку
     */
    public function test_search_route_works_for_unauthorized_users(): void
    {
        // Створюємо аніме для пошуку
        Anime::factory()->count(3)->create();

        // Перевіряємо маршрут
        $response = $this->getJson('/api/v1/search?query=anime');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'animes' => [
                        'data',
                        'meta',
                    ],
                    'people' => [
                        'data',
                        'meta',
                    ],
                    'studios' => [
                        'data',
                        'meta',
                    ],
                    'tags' => [
                        'data',
                        'meta',
                    ],
                ]
            ]);
    }
}
