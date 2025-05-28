<?php

namespace Tests\Feature\Api;

use AnimeSite\Models\Anime;
use AnimeSite\Models\Person;
use AnimeSite\Models\Studio;
use AnimeSite\Models\Tag;

class SearchTest extends ApiTestCase
{
    /**
     * Тест глобального пошуку (неавторизований користувач)
     */
    public function test_guest_can_perform_global_search(): void
    {
        // Створюємо тестові дані з однаковим ключовим словом
        $keyword = 'naruto';
        
        Anime::factory()->create([
            'name' => "The {$keyword} Series",
            'description' => 'Anime description',
        ]);
        
        Person::factory()->create([
            'name' => "Director of {$keyword}",
            'description' => 'Person description',
        ]);
        
        Studio::factory()->create([
            'name' => "{$keyword} Studio",
            'description' => 'Studio description',
        ]);
        
        Tag::factory()->create([
            'name' => "{$keyword} Genre",
            'description' => 'Tag description',
        ]);
        
        // Виконуємо запит
        $response = $this->getJson("/api/v1/search?query={$keyword}", $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonStructure([
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
        
        // Перевіряємо, що в кожній категорії є результати
        $response->assertJsonCount(1, 'data.animes.data');
        $response->assertJsonCount(1, 'data.people.data');
        $response->assertJsonCount(1, 'data.studios.data');
        $response->assertJsonCount(1, 'data.tags.data');
    }
    
    /**
     * Тест пошуку з фільтрацією за типом (неавторизований користувач)
     */
    public function test_guest_can_perform_filtered_search(): void
    {
        // Створюємо тестові дані з однаковим ключовим словом
        $keyword = 'naruto';
        
        Anime::factory()->create([
            'name' => "The {$keyword} Series",
            'description' => 'Anime description',
        ]);
        
        Person::factory()->create([
            'name' => "Director of {$keyword}",
            'description' => 'Person description',
        ]);
        
        // Виконуємо запит з фільтром за типом
        $response = $this->getJson("/api/v1/search?query={$keyword}&type=animes", $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'animes' => [
                    'data',
                    'meta',
                ],
            ]
        ]);
        
        // Перевіряємо, що є результати тільки для аніме
        $response->assertJsonCount(1, 'data.animes.data');
        $response->assertJsonMissing(['people', 'studios', 'tags']);
    }
    
    /**
     * Тест пошуку з пагінацією (неавторизований користувач)
     */
    public function test_guest_can_perform_paginated_search(): void
    {
        // Створюємо багато тестових даних з однаковим ключовим словом
        $keyword = 'popular';
        
        Anime::factory()->count(15)->create([
            'name' => "The {$keyword} Series",
            'description' => 'Anime description',
        ]);
        
        // Виконуємо запит з пагінацією
        $response = $this->getJson("/api/v1/search?query={$keyword}&type=animes&page=1&per_page=10", $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'animes' => [
                    'data',
                    'meta' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total'
                    ],
                ],
            ]
        ]);
        
        // Перевіряємо пагінацію
        $response->assertJsonCount(10, 'data.animes.data');
        $response->assertJsonPath('data.animes.meta.current_page', 1);
        $response->assertJsonPath('data.animes.meta.per_page', 10);
        $response->assertJsonPath('data.animes.meta.total', 15);
        
        // Перевіряємо другу сторінку
        $response = $this->getJson("/api/v1/search?query={$keyword}&type=animes&page=2&per_page=10", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data.animes.data');
        $response->assertJsonPath('data.animes.meta.current_page', 2);
    }
    
    /**
     * Тест пошуку з сортуванням (неавторизований користувач)
     */
    public function test_guest_can_perform_sorted_search(): void
    {
        // Створюємо тестові дані з різними датами
        $keyword = 'anime';
        
        Anime::factory()->create([
            'name' => "B {$keyword}",
            'first_air_date' => '2022-01-01',
            'imdb_score' => 7.5,
        ]);
        
        Anime::factory()->create([
            'name' => "A {$keyword}",
            'first_air_date' => '2023-01-01',
            'imdb_score' => 8.5,
        ]);
        
        Anime::factory()->create([
            'name' => "C {$keyword}",
            'first_air_date' => '2021-01-01',
            'imdb_score' => 9.0,
        ]);
        
        // Тестуємо сортування за назвою (за зростанням)
        $response = $this->getJson("/api/v1/search?query={$keyword}&type=animes&sort=name&sort_direction=asc", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonPath('data.animes.data.0.name', "A {$keyword}");
        
        // Тестуємо сортування за датою (за спаданням)
        $response = $this->getJson("/api/v1/search?query={$keyword}&type=animes&sort=first_air_date&sort_direction=desc", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonPath('data.animes.data.0.name', "A {$keyword}");
        
        // Тестуємо сортування за рейтингом (за спаданням)
        $response = $this->getJson("/api/v1/search?query={$keyword}&type=animes&sort=imdb_score&sort_direction=desc", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonPath('data.animes.data.0.name', "C {$keyword}");
    }
    
    /**
     * Тест пошуку з фільтрацією (неавторизований користувач)
     */
    public function test_guest_can_perform_search_with_filters(): void
    {
        // Створюємо студії
        $studio1 = Studio::factory()->create(['name' => 'Studio A']);
        $studio2 = Studio::factory()->create(['name' => 'Studio B']);
        
        // Створюємо теги
        $tag1 = Tag::factory()->create(['name' => 'Action', 'is_genre' => true]);
        $tag2 = Tag::factory()->create(['name' => 'Romance', 'is_genre' => true]);
        
        // Створюємо аніме з різними параметрами
        $keyword = 'test';
        
        $anime1 = Anime::factory()->create([
            'name' => "{$keyword} Anime 1",
            'kind' => 'tv_series',
            'status' => 'ongoing',
            'studio_id' => $studio1->id,
            'first_air_date' => '2022-01-01',
        ]);
        
        $anime2 = Anime::factory()->create([
            'name' => "{$keyword} Anime 2",
            'kind' => 'movie',
            'status' => 'completed',
            'studio_id' => $studio2->id,
            'first_air_date' => '2021-01-01',
        ]);
        
        $anime3 = Anime::factory()->create([
            'name' => "{$keyword} Anime 3",
            'kind' => 'tv_series',
            'status' => 'completed',
            'studio_id' => $studio1->id,
            'first_air_date' => '2023-01-01',
        ]);
        
        // Прив'язуємо теги
        $anime1->tags()->attach($tag1->id);
        $anime2->tags()->attach($tag2->id);
        $anime3->tags()->attach([$tag1->id, $tag2->id]);
        
        // Тестуємо фільтр за типом
        $response = $this->getJson("/api/v1/search?query={$keyword}&type=animes&kind=tv_series", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data.animes.data');
        
        // Тестуємо фільтр за статусом
        $response = $this->getJson("/api/v1/search?query={$keyword}&type=animes&status=completed", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data.animes.data');
        
        // Тестуємо фільтр за студією
        $response = $this->getJson("/api/v1/search?query={$keyword}&type=animes&studio_id={$studio1->id}", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data.animes.data');
        
        // Тестуємо фільтр за роком
        $response = $this->getJson("/api/v1/search?query={$keyword}&type=animes&year=2023", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data.animes.data');
        
        // Тестуємо фільтр за жанром
        $response = $this->getJson("/api/v1/search?query={$keyword}&type=animes&genre_ids[]={$tag1->id}", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data.animes.data');
        
        // Тестуємо комбінований фільтр
        $response = $this->getJson("/api/v1/search?query={$keyword}&type=animes&kind=tv_series&status=completed", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data.animes.data');
        $response->assertJsonPath('data.animes.data.0.name', "{$keyword} Anime 3");
    }
    
    /**
     * Тест пошуку з порожнім запитом
     */
    public function test_search_with_empty_query(): void
    {
        // Виконуємо запит з порожнім запитом
        $response = $this->getJson('/api/v1/search?query=', $this->guestHeaders());
        
        // Перевіряємо результат (повинна бути помилка валідації)
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['query']);
    }
}
