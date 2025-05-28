<?php

namespace Tests\Unit\Models;

use AnimeSite\Enums\Gender;
use AnimeSite\Enums\Role;
use AnimeSite\Enums\UserListType;
use AnimeSite\Models\Achievement;
use AnimeSite\Models\AchievementUser;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Comment;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Rating;
use AnimeSite\Models\Selection;
use AnimeSite\Models\User;
use AnimeSite\Models\UserList;
use AnimeSite\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_user()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => Role::USER,
            'gender' => Gender::MALE,
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => Role::USER->value,
            'gender' => Gender::MALE->value,
        ]);
    }

    /** @test */
    public function it_can_have_achievements()
    {
        $user = User::factory()->create();
        $achievements = Achievement::factory()->count(3)->create();

        // Прив'язуємо досягнення до користувача
        foreach ($achievements as $index => $achievement) {
            AchievementUser::factory()->create([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'progress_count' => $index + 1,
            ]);
        }

        // Перевіряємо, що досягнення прив'язані
        $this->assertCount(3, $user->achievements);
        $this->assertEquals(
            $achievements->pluck('id')->sort()->values(),
            $user->achievements->pluck('id')->sort()->values()
        );

        // Перевіряємо прогрес
        $this->assertEquals(1, $user->achievements->first()->pivot->progress_count);
    }

    /** @test */
    public function it_can_have_ratings()
    {
        $user = User::factory()->create();
        $animes = Anime::factory()->count(3)->create();

        // Створюємо оцінки
        foreach ($animes as $index => $anime) {
            Rating::create([
                'id' => \Illuminate\Support\Str::ulid(),
                'user_id' => $user->id,
                'anime_id' => $anime->id,
                'number' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Перевіряємо, що оцінки створені
        $this->assertCount(3, $user->ratings);

        // Перевіряємо значення оцінок
        $this->assertEquals(1, $user->ratings->first()->number);
    }

    /** @test */
    public function it_can_have_comments()
    {
        $user = User::factory()->create();
        $anime = Anime::factory()->create();

        // Створюємо коментарі
        $comments = [];
        for ($i = 0; $i < 3; $i++) {
            $comments[] = Comment::create([
                'user_id' => $user->id,
                'commentable_id' => $anime->id,
                'commentable_type' => Anime::class,
                'body' => "Comment {$i}",
                'is_spoiler' => false,
            ]);
        }

        // Перевіряємо, що коментарі створені
        $this->assertCount(3, $user->comments);
        $this->assertEquals(
            collect($comments)->pluck('id')->sort()->values(),
            $user->comments->pluck('id')->sort()->values()
        );
    }

    /** @test */
    public function it_can_have_watch_history()
    {
        $user = User::factory()->create();
        $episodes = Episode::factory()->count(3)->create();

        // Створюємо записи історії перегляду
        foreach ($episodes as $index => $episode) {
            WatchHistory::create([
                'id' => \Illuminate\Support\Str::ulid(),
                'user_id' => $user->id,
                'episode_id' => $episode->id,
                'progress_time' => ($index + 1) * 100,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Перевіряємо, що записи історії перегляду створені
        $this->assertCount(3, $user->watchHistories);
    }

    /** @test */
    public function it_can_have_selections()
    {
        $user = User::factory()->create();

        // Створюємо добірки
        $selections = [];
        for ($i = 0; $i < 3; $i++) {
            $selections[] = Selection::create([
                'user_id' => $user->id,
                'name' => "Selection {$i}",
                'slug' => "selection-{$i}",
                'description' => "Description {$i}",
            ]);
        }

        // Перевіряємо, що добірки створені
        $this->assertCount(3, $user->selections);
        $this->assertEquals(
            collect($selections)->pluck('id')->sort()->values(),
            $user->selections->pluck('id')->sort()->values()
        );
    }

    /** @test */
    public function it_can_have_user_lists()
    {
        $user = User::factory()->create();
        $anime = Anime::factory()->create();

        // Створюємо списки користувача
        UserList::create([
            'id' => \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
            'listable_id' => $anime->id,
            'listable_type' => 'AnimeSite\\Models\\Anime',
            'type' => UserListType::WATCHING,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Перевіряємо, що список створено
        $this->assertCount(1, $user->userLists);
        $this->assertEquals('watching', $user->userLists->first()->type);
    }

    /** @test */
    public function it_can_check_admin_role()
    {
        $admin = User::factory()->create(['role' => Role::ADMIN]);
        $user = User::factory()->create(['role' => Role::USER]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }

    /** @test */
    public function it_can_check_moderator_role()
    {
        $moderator = User::factory()->create(['role' => Role::MODERATOR]);
        $user = User::factory()->create(['role' => Role::USER]);

        $this->assertTrue($moderator->isModerator());
        $this->assertFalse($user->isModerator());
    }

    /** @test */
    public function it_can_update_user()
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        // Оновлюємо користувача
        $user->update([
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        // Перевіряємо, що дані оновлено
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /** @test */
    public function it_can_delete_user()
    {
        $user = User::factory()->create();
        $anime = Anime::factory()->create();

        // Створюємо оцінку
        $rating = Rating::create([
            'id' => \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
            'anime_id' => $anime->id,
            'number' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Видаляємо користувача
        $user->delete();

        // Перевіряємо, що користувача видалено
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        // Перевіряємо, що оцінку також видалено (через каскадне видалення)
        $this->assertDatabaseMissing('ratings', ['id' => $rating->id]);
    }

    /** @test */
    public function it_can_filter_by_role()
    {
        // Створюємо користувачів різних ролей
        User::factory()->count(2)->create(['role' => Role::ADMIN]);
        User::factory()->count(3)->create(['role' => Role::MODERATOR]);
        User::factory()->count(5)->create(['role' => Role::USER]);

        // Отримуємо користувачів за роллю
        $admins = User::where('role', Role::ADMIN->value)->get();
        $moderators = User::where('role', Role::MODERATOR->value)->get();
        $users = User::where('role', Role::USER->value)->get();

        // Перевіряємо кількість
        $this->assertCount(2, $admins);
        $this->assertCount(3, $moderators);
        $this->assertCount(5, $users);
    }

    /** @test */
    public function it_can_create_api_token()
    {
        $user = User::factory()->create();

        // Створюємо токен
        $token = $user->createToken('test-token');

        // Перевіряємо, що токен створено
        $this->assertNotNull($token);
        $this->assertNotEmpty($token->plainTextToken);

        // Перевіряємо, що токен збережено в базі
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
            'name' => 'test-token',
        ]);
    }
}
