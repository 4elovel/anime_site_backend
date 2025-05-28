<?php

namespace Tests\Unit\Models;

use AnimeSite\Models\Achievement;
use AnimeSite\Models\AchievementUser;
use AnimeSite\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_achievement()
    {
        $achievement = Achievement::factory()->create([
            'name' => 'Test Achievement',
            'description' => 'This is a test achievement',
            'max_counts' => 10,
        ]);

        $this->assertDatabaseHas('achievements', [
            'name' => 'Test Achievement',
            'description' => 'This is a test achievement',
            'max_counts' => 10,
        ]);
    }

    /** @test */
    public function it_can_be_assigned_to_user()
    {
        $achievement = Achievement::factory()->create();
        $user = User::factory()->create();

        // Створюємо зв'язок через модель AchievementUser
        AchievementUser::factory()->create([
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'progress_count' => 5,
        ]);

        // Оновлюємо дані користувача
        $user->refresh();

        // Перевіряємо, що зв'язок створено
        $this->assertDatabaseHas('achievement_user', [
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'progress_count' => 5,
        ]);

        // Перевіряємо, що досягнення доступне через зв'язок
        $this->assertTrue($user->achievements->contains($achievement));
        $this->assertEquals(5, $user->achievements->first()->pivot->progress_count);
    }

    /** @test */
    public function it_can_update_progress_count()
    {
        $achievement = Achievement::factory()->create();
        $user = User::factory()->create();

        // Створюємо зв'язок через модель AchievementUser
        $achievementUser = AchievementUser::factory()->create([
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'progress_count' => 5,
        ]);

        // Оновлюємо прогрес
        $achievementUser->update(['progress_count' => 8]);

        // Перевіряємо, що прогрес оновлено
        $this->assertDatabaseHas('achievement_user', [
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'progress_count' => 8,
        ]);

        // Оновлюємо дані користувача
        $user->refresh();

        // Перевіряємо, що прогрес оновлено в зв'язку
        $this->assertEquals(8, $user->achievements->first()->pivot->progress_count);
    }

    /** @test */
    public function it_can_be_detached_from_user()
    {
        $achievement = Achievement::factory()->create();
        $user = User::factory()->create();

        // Створюємо зв'язок через модель AchievementUser
        $achievementUser = AchievementUser::factory()->create([
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'progress_count' => 5,
        ]);

        // Видаляємо зв'язок
        $achievementUser->delete();

        // Перевіряємо, що зв'язок видалено
        $this->assertDatabaseMissing('achievement_user', [
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
        ]);

        // Оновлюємо дані користувача
        $user->refresh();

        // Перевіряємо, що досягнення більше не доступне через зв'язок
        $this->assertFalse($user->achievements->contains($achievement));
    }

    /** @test */
    public function it_can_retrieve_users_with_achievement()
    {
        $achievement = Achievement::factory()->create();
        $users = User::factory()->count(3)->create();

        // Створюємо зв'язки через модель AchievementUser
        foreach ($users as $index => $user) {
            AchievementUser::factory()->create([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'progress_count' => $index + 1,
            ]);
        }

        // Отримуємо користувачів через зв'язок
        $achievementUsers = $achievement->users;

        // Перевіряємо, що всі користувачі доступні через зв'язок
        $this->assertCount(3, $achievementUsers);
        $this->assertEquals($users->pluck('id')->sort()->values(), $achievementUsers->pluck('id')->sort()->values());
    }

    /** @test */
    public function it_can_be_soft_deleted()
    {
        $achievement = Achievement::factory()->create();
        $user = User::factory()->create();

        // Створюємо зв'язок через модель AchievementUser
        $achievementUser = AchievementUser::factory()->create([
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'progress_count' => 5,
        ]);

        // Видаляємо досягнення
        $achievement->delete();

        // Перевіряємо, що досягнення видалено з бази
        $this->assertDatabaseMissing('achievements', [
            'id' => $achievement->id,
        ]);

        // Перевіряємо, що зв'язок також видалено (через каскадне видалення)
        $this->assertDatabaseMissing('achievement_user', [
            'achievement_id' => $achievement->id,
        ]);
    }
}
