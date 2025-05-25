<?php

namespace Tests\Feature\Api;

use AnimeSite\Models\Tariff;
use AnimeSite\Models\UserSubscription;

class TariffTest extends ApiTestCase
{
    /**
     * Тест отримання списку тарифів (неавторизований користувач)
     */
    public function test_guest_can_get_tariff_list(): void
    {
        // Створюємо тестові дані
        Tariff::factory()->count(3)->create(['is_active' => true]);
        
        // Виконуємо запит
        $response = $this->getJson('/api/v1/tariffs', $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $this->assertPaginatedResponse($response);
        $response->assertJsonCount(3, 'data');
    }
    
    /**
     * Тест отримання деталей тарифу (неавторизований користувач)
     */
    public function test_guest_can_get_tariff_details(): void
    {
        // Створюємо тестові дані
        $tariff = Tariff::factory()->create(['is_active' => true]);
        
        // Виконуємо запит
        $response = $this->getJson("/api/v1/tariffs/{$tariff->slug}", $this->guestHeaders());
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id', 'slug', 'name', 'description', 'price', 'currency',
                'duration_days', 'is_active', 'features', 'meta'
            ]
        ]);
        $response->assertJsonPath('data.id', $tariff->id);
    }
    
    /**
     * Тест створення тарифу (авторизований адміністратор)
     */
    public function test_admin_can_create_tariff(): void
    {
        // Підготовка даних
        $tariffData = [
            'name' => 'Test Tariff',
            'description' => 'This is a test tariff description',
            'price' => 9.99,
            'currency' => 'USD',
            'duration_days' => 30,
            'is_active' => true,
            'features' => ['HD Quality', 'No Ads', 'Offline Viewing'],
        ];
        
        // Виконуємо запит
        $response = $this->postJson('/api/v1/tariffs', $tariffData, $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id', 'slug', 'name', 'description', 'price', 'currency',
                'duration_days', 'is_active', 'features'
            ]
        ]);
        
        // Перевіряємо, що дані збережені в базі
        $this->assertDatabaseHas('tariffs', [
            'name' => 'Test Tariff',
            'description' => 'This is a test tariff description',
            'price' => 9.99,
            'currency' => 'USD',
            'duration_days' => 30,
            'is_active' => true,
        ]);
    }
    
    /**
     * Тест оновлення тарифу (авторизований адміністратор)
     */
    public function test_admin_can_update_tariff(): void
    {
        // Створюємо тестові дані
        $tariff = Tariff::factory()->create();
        
        // Підготовка даних для оновлення
        $updateData = [
            'name' => 'Updated Tariff Name',
            'description' => 'Updated description',
            'price' => 19.99,
            'is_active' => false,
        ];
        
        // Виконуємо запит
        $response = $this->putJson("/api/v1/tariffs/{$tariff->slug}", $updateData, $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Updated Tariff Name');
        $response->assertJsonPath('data.description', 'Updated description');
        $response->assertJsonPath('data.price', 19.99);
        $response->assertJsonPath('data.is_active', false);
        
        // Перевіряємо, що дані оновлені в базі
        $this->assertDatabaseHas('tariffs', [
            'id' => $tariff->id,
            'name' => 'Updated Tariff Name',
            'description' => 'Updated description',
            'price' => 19.99,
            'is_active' => false,
        ]);
    }
    
    /**
     * Тест видалення тарифу (авторизований адміністратор)
     */
    public function test_admin_can_delete_tariff(): void
    {
        // Створюємо тестові дані
        $tariff = Tariff::factory()->create();
        
        // Виконуємо запит
        $response = $this->deleteJson("/api/v1/tariffs/{$tariff->slug}", [], $this->authHeaders($this->admin));
        
        // Перевіряємо результат
        $response->assertStatus(204);
        
        // Перевіряємо, що запис видалено з бази
        $this->assertDatabaseMissing('tariffs', [
            'id' => $tariff->id,
        ]);
    }
    
    /**
     * Тест підписки користувача на тариф
     */
    public function test_user_can_subscribe_to_tariff(): void
    {
        // Створюємо тестові дані
        $tariff = Tariff::factory()->create(['is_active' => true]);
        
        // Підготовка даних
        $subscriptionData = [
            'tariff_id' => $tariff->id,
            'payment_method' => 'credit_card',
            'auto_renew' => true,
        ];
        
        // Виконуємо запит
        $response = $this->postJson('/api/v1/subscriptions', $subscriptionData, $this->authHeaders($this->user));
        
        // Перевіряємо результат
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id', 'user_id', 'tariff_id', 'start_date', 'end_date',
                'is_active', 'auto_renew'
            ]
        ]);
        
        // Перевіряємо, що підписка створена в базі
        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $this->user->id,
            'tariff_id' => $tariff->id,
            'is_active' => true,
            'auto_renew' => true,
        ]);
    }
    
    /**
     * Тест отримання активних підписок користувача
     */
    public function test_user_can_get_active_subscriptions(): void
    {
        // Створюємо тестові дані
        $tariff = Tariff::factory()->create(['is_active' => true]);
        
        // Створюємо підписку
        UserSubscription::factory()->create([
            'user_id' => $this->user->id,
            'tariff_id' => $tariff->id,
            'is_active' => true,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);
        
        // Виконуємо запит
        $response = $this->getJson('/api/v1/subscriptions/active', $this->authHeaders($this->user));
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id', 'user_id', 'tariff_id', 'start_date', 'end_date',
                    'is_active', 'auto_renew'
                ]
            ]
        ]);
        $response->assertJsonCount(1, 'data');
    }
    
    /**
     * Тест скасування підписки користувача
     */
    public function test_user_can_cancel_subscription(): void
    {
        // Створюємо тестові дані
        $tariff = Tariff::factory()->create(['is_active' => true]);
        
        // Створюємо підписку
        $subscription = UserSubscription::factory()->create([
            'user_id' => $this->user->id,
            'tariff_id' => $tariff->id,
            'is_active' => true,
            'auto_renew' => true,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);
        
        // Виконуємо запит
        $response = $this->putJson("/api/v1/subscriptions/{$subscription->id}/cancel", [], $this->authHeaders($this->user));
        
        // Перевіряємо результат
        $response->assertStatus(200);
        $response->assertJsonPath('data.auto_renew', false);
        
        // Перевіряємо, що підписка оновлена в базі
        $this->assertDatabaseHas('user_subscriptions', [
            'id' => $subscription->id,
            'auto_renew' => false,
        ]);
    }
    
    /**
     * Тест фільтрації тарифів
     */
    public function test_tariff_filtering(): void
    {
        // Створюємо тарифи з різними параметрами
        Tariff::factory()->create([
            'name' => 'Basic',
            'price' => 4.99,
            'duration_days' => 30,
            'is_active' => true,
        ]);
        
        Tariff::factory()->create([
            'name' => 'Premium',
            'price' => 9.99,
            'duration_days' => 30,
            'is_active' => true,
        ]);
        
        Tariff::factory()->create([
            'name' => 'Annual',
            'price' => 99.99,
            'duration_days' => 365,
            'is_active' => false,
        ]);
        
        // Тестуємо фільтр за активністю
        $response = $this->getJson('/api/v1/tariffs?is_active=true', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        
        // Тестуємо фільтр за тривалістю
        $response = $this->getJson('/api/v1/tariffs?duration_days=365', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', 'Annual');
    }
    
    /**
     * Тест сортування тарифів
     */
    public function test_tariff_sorting(): void
    {
        // Створюємо тарифи з різними цінами
        Tariff::factory()->create([
            'name' => 'Medium',
            'price' => 9.99,
            'is_active' => true,
        ]);
        
        Tariff::factory()->create([
            'name' => 'Basic',
            'price' => 4.99,
            'is_active' => true,
        ]);
        
        Tariff::factory()->create([
            'name' => 'Premium',
            'price' => 14.99,
            'is_active' => true,
        ]);
        
        // Тестуємо сортування за ціною (за зростанням)
        $response = $this->getJson('/api/v1/tariffs?sort_by=price&sort_direction=asc', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.name', 'Basic');
        $response->assertJsonPath('data.1.name', 'Medium');
        $response->assertJsonPath('data.2.name', 'Premium');
        
        // Тестуємо сортування за ціною (за спаданням)
        $response = $this->getJson('/api/v1/tariffs?sort_by=price&sort_direction=desc', $this->guestHeaders());
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.name', 'Premium');
        $response->assertJsonPath('data.1.name', 'Medium');
        $response->assertJsonPath('data.2.name', 'Basic');
    }
}
