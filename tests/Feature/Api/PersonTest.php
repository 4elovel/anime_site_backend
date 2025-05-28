<?php

namespace Tests\Feature\Api;

use AnimeSite\Models\Person;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Tag;

class PersonTest extends ApiTestCase
{
    /**
     * Тест отримання списку персон (неавторизований користувач)
     */
    public function test_guest_can_get_person_list(): void
    {
        // Створюємо тестові дані
        Person::factory()->count(5)->create();
        
        // Виконуємо запит
        $response = $this->getJson('/api/v1/people', $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(5, 'data');
    }
    
    /**
     * Тест отримання деталей персони (неавторизований користувач)
     */
    public function test_guest_can_get_person_details(): void
    {
        // Створюємо тестові дані
        $person = Person::factory()->create();
        
        // Виконуємо запит
        $response = $this->getJson("/api/v1/people/{$person->slug}", $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id', 'slug', 'name', 'description', 'photo', 'birth_date',
                'death_date', 'meta'
            ]
        ]);
        $response->assertJsonPath('data.id', $person->id);
    }
    
    /**
     * Тест створення персони (авторизований адміністратор)
     */
    public function test_admin_can_create_person(): void
    {
        // Підготовка даних
        $personData = [
            'name' => 'Test Person',
            'description' => 'This is a test person description',
            'birth_date' => '1980-01-01',
            'gender' => 'male',
            'country' => 'Japan',
        ];
        
        // Виконуємо запит
        $response = $this->postJson('/api/v1/people', $personData, $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id', 'slug', 'name', 'description', 'birth_date', 'gender', 'country'
            ]
        ]);
        
        // Перевіряємо, що дані збережені в базі
        $this->assertDatabaseHas('people', [
            'name' => 'Test Person',
            'description' => 'This is a test person description',
            'birth_date' => '1980-01-01',
            'gender' => 'male',
            'country' => 'Japan',
        ]);
    }
    
    /**
     * Тест оновлення персони (авторизований адміністратор)
     */
    public function test_admin_can_update_person(): void
    {
        // Створюємо тестові дані
        $person = Person::factory()->create();
        
        // Підготовка даних для оновлення
        $updateData = [
            'name' => 'Updated Person Name',
            'description' => 'Updated description',
            'country' => 'USA',
        ];
        
        // Виконуємо запит
        $response = $this->putJson("/api/v1/people/{$person->slug}", $updateData, $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Updated Person Name');
        $response->assertJsonPath('data.description', 'Updated description');
        $response->assertJsonPath('data.country', 'USA');
        
        // Перевіряємо, що дані оновлені в базі
        $this->assertDatabaseHas('people', [
            'id' => $person->id,
            'name' => 'Updated Person Name',
            'description' => 'Updated description',
            'country' => 'USA',
        ]);
    }
    
    /**
     * Тест видалення персони (авторизований адміністратор)
     */
    public function test_admin_can_delete_person(): void
    {
        // Створюємо тестові дані
        $person = Person::factory()->create();
        
        // Виконуємо запит
        $response = $this->deleteJson("/api/v1/people/{$person->slug}", [], $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(204);
        
        // Перевіряємо, що запис видалено з бази
        $this->assertDatabaseMissing('people', [
            'id' => $person->id,
        ]);
    }
    
    /**
     * Тест фільтрації персон
     */
    public function test_person_filtering(): void
    {
        // Створюємо персони з різними параметрами
        Person::factory()->create([
            'name' => 'John Director',
            'gender' => 'male',
            'country' => 'USA',
        ]);
        
        Person::factory()->create([
            'name' => 'Jane Director',
            'gender' => 'female',
            'country' => 'USA',
        ]);
        
        Person::factory()->create([
            'name' => 'Akira Director',
            'gender' => 'male',
            'country' => 'Japan',
        ]);
        
        // Тестуємо фільтр за статтю
        $response = $this->getJson('/api/v1/people?gender=male', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        
        // Тестуємо фільтр за країною
        $response = $this->getJson('/api/v1/people?country=Japan', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        
        // Тестуємо комбінований фільтр
        $response = $this->getJson('/api/v1/people?gender=male&country=USA', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
    
    /**
     * Тест пошуку персон
     */
    public function test_person_search(): void
    {
        // Створюємо персони з різними іменами
        Person::factory()->create(['name' => 'Hayao Miyazaki']);
        Person::factory()->create(['name' => 'Makoto Shinkai']);
        Person::factory()->create(['name' => 'Mamoru Hosoda']);
        
        // Тестуємо пошук
        $response = $this->getJson('/api/v1/people?search=makoto', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        
        $response = $this->getJson('/api/v1/people?search=ma', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }
    
    /**
     * Тест зв'язку персони з аніме
     */
    public function test_person_anime_relationship(): void
    {
        // Створюємо персону і аніме
        $person = Person::factory()->create();
        $animes = Anime::factory()->count(3)->create();
        
        // Прив'язуємо аніме до персони (як режисер)
        foreach ($animes as $anime) {
            $anime->directors()->attach($person->id);
        }
        
        // Перевіряємо, що аніме прив'язані
        $this->assertEquals(3, $person->directedAnimes()->count());
        
        // Перевіряємо API-запит
        $response = $this->getJson("/api/v1/people/{$person->slug}/animes", $this->guestHeaders());
        $response->assertStatus(200);
        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(3, 'data');
    }
    
    /**
     * Тест зв'язку персони з тегами
     */
    public function test_person_tags_relationship(): void
    {
        // Створюємо персону і теги
        $person = Person::factory()->create();
        $tags = Tag::factory()->count(3)->create();
        
        // Прив'язуємо теги до персони
        $person->tags()->attach($tags->pluck('id')->toArray());
        
        // Перевіряємо, що теги прив'язані
        $this->assertEquals(3, $person->tags()->count());
        
        // Перевіряємо API-запит
        $response = $this->getJson("/api/v1/people/{$person->slug}", $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'tags',
            ]
        ]);
        $response->assertJsonCount(3, 'data.tags');
    }
}
