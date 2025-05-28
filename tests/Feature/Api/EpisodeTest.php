<?php

namespace Tests\Feature\Api;

use AnimeSite\Models\Anime;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Comment;

class EpisodeTest extends ApiTestCase
{
    /**
     * Тест отримання списку епізодів (неавторизований користувач)
     */
    public function test_guest_can_get_episode_list(): void
    {
        // Створюємо тестові дані
        $anime = Anime::factory()->create();
        Episode::factory()->count(5)->create(['anime_id' => $anime->id]);
        
        // Виконуємо запит
        $response = $this->getJson('/api/v1/episodes', $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(5, 'data');
    }
    
    /**
     * Тест отримання останніх епізодів (неавторизований користувач)
     */
    public function test_guest_can_get_latest_episodes(): void
    {
        // Створюємо тестові дані з різними датами
        $anime = Anime::factory()->create();
        Episode::factory()->create([
            'anime_id' => $anime->id,
            'air_date' => now()->subDays(10),
        ]);
        Episode::factory()->create([
            'anime_id' => $anime->id,
            'air_date' => now()->subDays(5),
        ]);
        Episode::factory()->create([
            'anime_id' => $anime->id,
            'air_date' => now()->subDays(1),
        ]);
        
        // Виконуємо запит
        $response = $this->getJson('/api/v1/episodes/latest', $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $this->assertPaginatedResponse($response);
        
        // Перевіряємо, що епізоди відсортовані за датою (найновіші спочатку)
        $episodes = $response->json('data');
        $this->assertTrue(
            strtotime($episodes[0]['air_date']) > strtotime($episodes[1]['air_date'])
        );
    }
    
    /**
     * Тест отримання деталей епізоду (неавторизований користувач)
     */
    public function test_guest_can_get_episode_details(): void
    {
        // Створюємо тестові дані
        $anime = Anime::factory()->create();
        $episode = Episode::factory()->create(['anime_id' => $anime->id]);
        
        // Виконуємо запит
        $response = $this->getJson("/api/v1/episodes/{$episode->slug}", $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id', 'slug', 'name', 'description', 'number', 'duration',
                'air_date', 'anime_id', 'video_players', 'meta'
            ]
        ]);
        $response->assertJsonPath('data.id', $episode->id);
    }
    
    /**
     * Тест створення епізоду (авторизований адміністратор)
     */
    public function test_admin_can_create_episode(): void
    {
        // Створюємо аніме для зв'язку
        $anime = Anime::factory()->create();
        
        // Підготовка даних
        $episodeData = [
            'name' => 'Test Episode',
            'description' => 'This is a test episode description',
            'number' => 1,
            'duration' => 24,
            'air_date' => '2023-01-01',
            'anime_id' => $anime->id,
            'video_players' => [
                [
                    'name' => 'kodik',
                    'url' => 'https://example.com/video1',
                    'file_url' => 'https://example.com/file1',
                    'dubbing' => 'en',
                    'quality' => 'hd',
                    'locale_code' => 'en'
                ]
            ]
        ];
        
        // Виконуємо запит
        $response = $this->postJson('/api/v1/episodes', $episodeData, $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id', 'slug', 'name', 'description', 'number', 'duration',
                'air_date', 'anime_id', 'video_players'
            ]
        ]);
        
        // Перевіряємо, що дані збережені в базі
        $this->assertDatabaseHas('episodes', [
            'name' => 'Test Episode',
            'number' => 1,
            'anime_id' => $anime->id,
        ]);
    }
    
    /**
     * Тест оновлення епізоду (авторизований адміністратор)
     */
    public function test_admin_can_update_episode(): void
    {
        // Створюємо тестові дані
        $anime = Anime::factory()->create();
        $episode = Episode::factory()->create(['anime_id' => $anime->id]);
        
        // Підготовка даних для оновлення
        $updateData = [
            'name' => 'Updated Episode Name',
            'description' => 'Updated description',
            'duration' => 30,
        ];
        
        // Виконуємо запит
        $response = $this->putJson("/api/v1/episodes/{$episode->slug}", $updateData, $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Updated Episode Name');
        $response->assertJsonPath('data.description', 'Updated description');
        $response->assertJsonPath('data.duration', 30);
        
        // Перевіряємо, що дані оновлені в базі
        $this->assertDatabaseHas('episodes', [
            'id' => $episode->id,
            'name' => 'Updated Episode Name',
            'description' => 'Updated description',
            'duration' => 30,
        ]);
    }
    
    /**
     * Тест видалення епізоду (авторизований адміністратор)
     */
    public function test_admin_can_delete_episode(): void
    {
        // Створюємо тестові дані
        $anime = Anime::factory()->create();
        $episode = Episode::factory()->create(['anime_id' => $anime->id]);
        
        // Виконуємо запит
        $response = $this->deleteJson("/api/v1/episodes/{$episode->slug}", [], $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(204);
        
        // Перевіряємо, що запис видалено з бази
        $this->assertDatabaseMissing('episodes', [
            'id' => $episode->id,
        ]);
    }
    
    /**
     * Тест фільтрації епізодів
     */
    public function test_episode_filtering(): void
    {
        // Створюємо аніме
        $anime1 = Anime::factory()->create();
        $anime2 = Anime::factory()->create();
        
        // Створюємо епізоди з різними параметрами
        Episode::factory()->create([
            'anime_id' => $anime1->id,
            'number' => 1,
            'is_filler' => true,
        ]);
        
        Episode::factory()->create([
            'anime_id' => $anime1->id,
            'number' => 2,
            'is_filler' => false,
        ]);
        
        Episode::factory()->create([
            'anime_id' => $anime2->id,
            'number' => 1,
            'is_filler' => false,
        ]);
        
        // Тестуємо фільтр за аніме
        $response = $this->getJson("/api/v1/episodes?anime_id={$anime1->id}", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        
        // Тестуємо фільтр за filler
        $response = $this->getJson('/api/v1/episodes?is_filler=true', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        
        // Тестуємо комбінований фільтр
        $response = $this->getJson("/api/v1/episodes?anime_id={$anime1->id}&is_filler=false", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
    
    /**
     * Тест отримання коментарів до епізоду
     */
    public function test_get_episode_comments(): void
    {
        // Створюємо тестові дані
        $anime = Anime::factory()->create();
        $episode = Episode::factory()->create(['anime_id' => $anime->id]);
        
        // Створюємо коментарі
        Comment::factory()->count(3)->create([
            'commentable_type' => Episode::class,
            'commentable_id' => $episode->id,
            'user_id' => $this->user->id,
        ]);
        
        // Виконуємо запит
        $response = $this->getJson("/api/v1/episodes/{$episode->slug}/comments", $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(3, 'data');
    }
    
    /**
     * Тест додавання коментаря до епізоду (авторизований користувач)
     */
    public function test_user_can_add_comment_to_episode(): void
    {
        // Створюємо тестові дані
        $anime = Anime::factory()->create();
        $episode = Episode::factory()->create(['anime_id' => $anime->id]);
        
        // Підготовка даних
        $commentData = [
            'content' => 'This is a test comment',
        ];
        
        // Виконуємо запит
        $response = $this->postJson("/api/v1/episodes/{$episode->slug}/comments", $commentData, $this->authHeaders($this->user));
        
        // Перевіряємо результат
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id', 'content', 'user_id', 'created_at'
            ]
        ]);
        
        // Перевіряємо, що коментар збережено в базі
        $this->assertDatabaseHas('comments', [
            'content' => 'This is a test comment',
            'commentable_type' => Episode::class,
            'commentable_id' => $episode->id,
            'user_id' => $this->user->id,
        ]);
    }
}
