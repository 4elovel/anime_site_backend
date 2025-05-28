<?php

namespace Tests\Feature\Api;

use AnimeSite\Models\Selection;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Tag;

class SelectionTest extends ApiTestCase
{
    /**
     * Тест отримання списку добірок (неавторизований користувач)
     */
    public function test_guest_can_get_selection_list(): void
    {
        // Створюємо тестові дані
        Selection::factory()->count(5)->create(['is_active' => true]);
        
        // Виконуємо запит
        $response = $this->getJson('/api/v1/selections', $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(5, 'data');
    }
    
    /**
     * Тест отримання деталей добірки (неавторизований користувач)
     */
    public function test_guest_can_get_selection_details(): void
    {
        // Створюємо тестові дані
        $selection = Selection::factory()->create(['is_active' => true]);
        
        // Виконуємо запит
        $response = $this->getJson("/api/v1/selections/{$selection->slug}", $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id', 'slug', 'name', 'description', 'poster', 'is_active',
                'meta'
            ]
        ]);
        $response->assertJsonPath('data.id', $selection->id);
    }
    
    /**
     * Тест створення добірки (авторизований адміністратор)
     */
    public function test_admin_can_create_selection(): void
    {
        // Підготовка даних
        $selectionData = [
            'name' => 'Test Selection',
            'description' => 'This is a test selection description',
            'is_active' => true,
        ];
        
        // Виконуємо запит
        $response = $this->postJson('/api/v1/selections', $selectionData, $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id', 'slug', 'name', 'description', 'is_active'
            ]
        ]);
        
        // Перевіряємо, що дані збережені в базі
        $this->assertDatabaseHas('selections', [
            'name' => 'Test Selection',
            'description' => 'This is a test selection description',
            'is_active' => true,
        ]);
    }
    
    /**
     * Тест оновлення добірки (авторизований адміністратор)
     */
    public function test_admin_can_update_selection(): void
    {
        // Створюємо тестові дані
        $selection = Selection::factory()->create();
        
        // Підготовка даних для оновлення
        $updateData = [
            'name' => 'Updated Selection Name',
            'description' => 'Updated description',
            'is_active' => false,
        ];
        
        // Виконуємо запит
        $response = $this->putJson("/api/v1/selections/{$selection->slug}", $updateData, $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Updated Selection Name');
        $response->assertJsonPath('data.description', 'Updated description');
        $response->assertJsonPath('data.is_active', false);
        
        // Перевіряємо, що дані оновлені в базі
        $this->assertDatabaseHas('selections', [
            'id' => $selection->id,
            'name' => 'Updated Selection Name',
            'description' => 'Updated description',
            'is_active' => false,
        ]);
    }
    
    /**
     * Тест видалення добірки (авторизований адміністратор)
     */
    public function test_admin_can_delete_selection(): void
    {
        // Створюємо тестові дані
        $selection = Selection::factory()->create();
        
        // Виконуємо запит
        $response = $this->deleteJson("/api/v1/selections/{$selection->slug}", [], $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(204);
        
        // Перевіряємо, що запис видалено з бази
        $this->assertDatabaseMissing('selections', [
            'id' => $selection->id,
        ]);
    }
    
    /**
     * Тест додавання аніме до добірки (авторизований адміністратор)
     */
    public function test_admin_can_add_anime_to_selection(): void
    {
        // Створюємо тестові дані
        $selection = Selection::factory()->create();
        $anime = Anime::factory()->create();
        
        // Підготовка даних
        $data = [
            'anime_id' => $anime->id,
        ];
        
        // Виконуємо запит
        $response = $this->postJson("/api/v1/selections/{$selection->slug}/animes", $data, $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(201);
        
        // Перевіряємо, що зв'язок створено в базі
        $this->assertDatabaseHas('selectionables', [
            'selection_id' => $selection->id,
            'selectionable_type' => Anime::class,
            'selectionable_id' => $anime->id,
        ]);
    }
    
    /**
     * Тест видалення аніме з добірки (авторизований адміністратор)
     */
    public function test_admin_can_remove_anime_from_selection(): void
    {
        // Створюємо тестові дані
        $selection = Selection::factory()->create();
        $anime = Anime::factory()->create();
        
        // Додаємо аніме до добірки
        $selection->animes()->attach($anime->id);
        
        // Виконуємо запит
        $response = $this->deleteJson("/api/v1/selections/{$selection->slug}/animes/{$anime->id}", [], $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(204);
        
        // Перевіряємо, що зв'язок видалено з бази
        $this->assertDatabaseMissing('selectionables', [
            'selection_id' => $selection->id,
            'selectionable_type' => Anime::class,
            'selectionable_id' => $anime->id,
        ]);
    }
    
    /**
     * Тест отримання аніме з добірки
     */
    public function test_get_animes_from_selection(): void
    {
        // Створюємо тестові дані
        $selection = Selection::factory()->create(['is_active' => true]);
        $animes = Anime::factory()->count(3)->create();
        
        // Додаємо аніме до добірки
        foreach ($animes as $anime) {
            $selection->animes()->attach($anime->id);
        }
        
        // Виконуємо запит
        $response = $this->getJson("/api/v1/selections/{$selection->slug}/animes", $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(3, 'data');
    }
    
    /**
     * Тест фільтрації добірок
     */
    public function test_selection_filtering(): void
    {
        // Створюємо добірки з різними параметрами
        Selection::factory()->create([
            'name' => 'Active Selection',
            'is_active' => true,
        ]);
        
        Selection::factory()->create([
            'name' => 'Inactive Selection',
            'is_active' => false,
        ]);
        
        // Тестуємо фільтр за активністю
        $response = $this->getJson('/api/v1/selections?is_active=true', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', 'Active Selection');
    }
    
    /**
     * Тест пошуку добірок
     */
    public function test_selection_search(): void
    {
        // Створюємо добірки з різними назвами
        Selection::factory()->create(['name' => 'Best Anime of 2023', 'is_active' => true]);
        Selection::factory()->create(['name' => 'Top Romance Anime', 'is_active' => true]);
        Selection::factory()->create(['name' => 'Must Watch Anime', 'is_active' => true]);
        
        // Тестуємо пошук
        $response = $this->getJson('/api/v1/selections?search=romance', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        
        $response = $this->getJson('/api/v1/selections?search=anime', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }
    
    /**
     * Тест зв'язку добірки з тегами
     */
    public function test_selection_tags_relationship(): void
    {
        // Створюємо добірку і теги
        $selection = Selection::factory()->create(['is_active' => true]);
        $tags = Tag::factory()->count(3)->create();
        
        // Прив'язуємо теги до добірки
        $selection->tags()->attach($tags->pluck('id')->toArray());
        
        // Перевіряємо, що теги прив'язані
        $this->assertEquals(3, $selection->tags()->count());
        
        // Перевіряємо API-запит
        $response = $this->getJson("/api/v1/selections/{$selection->slug}", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'tags',
            ]
        ]);
        $response->assertJsonCount(3, 'data.tags');
    }
}
