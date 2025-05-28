<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use AnimeSite\Models\User;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $user;
    
    /**
     * Налаштування перед кожним тестом
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Створюємо адміністратора
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
        
        // Створюємо звичайного користувача
        $this->user = User::factory()->create([
            'role' => 'user',
        ]);
    }
    
    /**
     * Отримати заголовки для авторизованого запиту
     */
    protected function authHeaders(User $user = null): array
    {
        $user = $user ?? $this->user;
        $token = $user->createToken('test-token')->plainTextToken;
        
        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];
    }
    
    /**
     * Отримати заголовки для неавторизованого запиту
     */
    protected function guestHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }
    
    /**
     * Перевірити структуру відповіді з пагінацією
     */
    protected function assertPaginatedResponse($response): void
    {
        $response->assertJsonStructure([
            'data',
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total'
            ]
        ]);
    }
}
