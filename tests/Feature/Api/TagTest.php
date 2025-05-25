<?php

namespace Tests\Feature\Api;

use AnimeSite\Models\Tag;
use AnimeSite\Models\Anime;

class TagTest extends ApiTestCase
{
    /**
     * Тест отримання списку тегів (неавторизований користувач)
     */
    public function test_guest_can_get_tag_list(): void
    {
        // Створюємо тестові дані
        Tag::factory()->count(5)->create();
        
        // Виконуємо запит
        $response = $this->getJson('/api/v1/tags', $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(5, 'data');
    }
    
    /**
     * Тест отримання деталей тегу (неавторизований користувач)
     */
    public function test_guest_can_get_tag_details(): void
    {
        // Створюємо тестові дані
        $tag = Tag::factory()->create();
        
        // Виконуємо запит
        $response = $this->getJson("/api/v1/tags/{$tag->slug}", $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id', 'slug', 'name', 'description', 'image', 'aliases',
                'is_genre', 'meta'
            ]
        ]);
        $response->assertJsonPath('data.id', $tag->id);
    }
    
    /**
     * Тест створення тегу (авторизований адміністратор)
     */
    public function test_admin_can_create_tag(): void
    {
        // Підготовка даних
        $tagData = [
            'name' => 'Test Tag',
            'description' => 'This is a test tag description',
            'is_genre' => true,
            'aliases' => ['test', 'tag', 'example'],
        ];
        
        // Виконуємо запит
        $response = $this->postJson('/api/v1/tags', $tagData, $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id', 'slug', 'name', 'description', 'is_genre', 'aliases'
            ]
        ]);
        
        // Перевіряємо, що дані збережені в базі
        $this->assertDatabaseHas('tags', [
            'name' => 'Test Tag',
            'description' => 'This is a test tag description',
            'is_genre' => true,
        ]);
    }
    
    /**
     * Тест оновлення тегу (авторизований адміністратор)
     */
    public function test_admin_can_update_tag(): void
    {
        // Створюємо тестові дані
        $tag = Tag::factory()->create();
        
        // Підготовка даних для оновлення
        $updateData = [
            'name' => 'Updated Tag Name',
            'description' => 'Updated description',
            'is_genre' => false,
        ];
        
        // Виконуємо запит
        $response = $this->putJson("/api/v1/tags/{$tag->slug}", $updateData, $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Updated Tag Name');
        $response->assertJsonPath('data.description', 'Updated description');
        $response->assertJsonPath('data.is_genre', false);
        
        // Перевіряємо, що дані оновлені в базі
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Updated Tag Name',
            'description' => 'Updated description',
            'is_genre' => false,
        ]);
    }
    
    /**
     * Тест видалення тегу (авторизований адміністратор)
     */
    public function test_admin_can_delete_tag(): void
    {
        // Створюємо тестові дані
        $tag = Tag::factory()->create();
        
        // Виконуємо запит
        $response = $this->deleteJson("/api/v1/tags/{$tag->slug}", [], $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(204);
        
        // Перевіряємо, що запис видалено з бази
        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }
    
    /**
     * Тест фільтрації тегів
     */
    public function test_tag_filtering(): void
    {
        // Створюємо теги з різними параметрами
        Tag::factory()->create([
            'name' => 'Action',
            'is_genre' => true,
        ]);
        
        Tag::factory()->create([
            'name' => 'Romance',
            'is_genre' => true,
        ]);
        
        Tag::factory()->create([
            'name' => 'School',
            'is_genre' => false,
        ]);
        
        // Тестуємо фільтр за жанром
        $response = $this->getJson('/api/v1/tags?is_genre=true', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        
        // Тестуємо пошук за назвою
        $response = $this->getJson('/api/v1/tags?search=action', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
    
    /**
     * Тест отримання аніме за тегом
     */
    public function test_get_anime_by_tag(): void
    {
        // Створюємо тестові дані
        $tag = Tag::factory()->create();
        $animes = Anime::factory()->count(3)->create();
        
        // Прив'язуємо аніме до тегу
        foreach ($animes as $anime) {
            $anime->tags()->attach($tag->id);
        }
        
        // Виконуємо запит
        $response = $this->getJson("/api/v1/tags/{$tag->slug}/animes", $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(3, 'data');
    }
    
    /**
     * Тест отримання популярних тегів
     */
    public function test_get_popular_tags(): void
    {
        // Створюємо теги
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $tag3 = Tag::factory()->create();
        
        // Створюємо аніме і прив'язуємо теги з різною частотою
        $animes = Anime::factory()->count(5)->create();
        
        foreach ($animes as $anime) {
            $anime->tags()->attach($tag1->id);
        }
        
        $animes->take(3)->each(function ($anime) use ($tag2) {
            $anime->tags()->attach($tag2->id);
        });
        
        $animes->take(1)->each(function ($anime) use ($tag3) {
            $anime->tags()->attach($tag3->id);
        });
        
        // Виконуємо запит
        $response = $this->getJson('/api/v1/tags/popular', $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        
        // Перевіряємо порядок (за популярністю)
        $tags = $response->json('data');
        $this->assertEquals($tag1->id, $tags[0]['id']);
        $this->assertEquals($tag2->id, $tags[1]['id']);
        $this->assertEquals($tag3->id, $tags[2]['id']);
    }
}
