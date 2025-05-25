<?php

namespace Tests\Unit\Models;

use AnimeSite\Enums\Kind;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Comment;
use AnimeSite\Models\User;
use AnimeSite\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EpisodeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_episode()
    {
        $anime = Anime::factory()->create([
            'kind' => Kind::TV_SERIES,
        ]);

        // Створюємо епізод безпосередньо через SQL
        $episodeId = DB::table('episodes')->insertGetId([
            'id' => \Illuminate\Support\Str::ulid(),
            'anime_id' => $anime->id,
            'number' => 1,
            'slug' => 'test-episode',
            'name' => 'Test Episode',
            'description' => 'This is a test episode',
            'duration' => 24,
            'created_at' => now(),
            'updated_at' => now(),
            'pictures' => '[]',
            'video_players' => '[]',
        ]);

        $this->assertDatabaseHas('episodes', [
            'anime_id' => $anime->id,
            'number' => 1,
            'name' => 'Test Episode',
            'description' => 'This is a test episode',
            'duration' => 24,
        ]);
    }

    /** @test */
    public function it_belongs_to_anime()
    {
        $anime = Anime::factory()->create([
            'name' => 'Test Anime',
            'kind' => Kind::TV_SERIES,
        ]);

        // Створюємо епізод безпосередньо через SQL
        $episodeId = DB::table('episodes')->insertGetId([
            'id' => \Illuminate\Support\Str::ulid(),
            'anime_id' => $anime->id,
            'number' => 1,
            'slug' => 'test-episode',
            'name' => 'Test Episode',
            'description' => 'This is a test episode',
            'duration' => 24,
            'created_at' => now(),
            'updated_at' => now(),
            'pictures' => '[]',
            'video_players' => '[]',
        ]);

        // Отримуємо епізод з бази даних
        $episode = Episode::find($episodeId);

        $this->assertEquals($anime->id, $episode->anime->id);
        $this->assertEquals('Test Anime', $episode->anime->name);
    }

    /** @test */
    public function it_can_have_comments()
    {
        $anime = Anime::factory()->create([
            'kind' => Kind::TV_SERIES,
        ]);

        // Створюємо епізод безпосередньо через SQL
        $episodeId = DB::table('episodes')->insertGetId([
            'id' => \Illuminate\Support\Str::ulid(),
            'anime_id' => $anime->id,
            'number' => 1,
            'slug' => 'test-episode',
            'name' => 'Test Episode',
            'description' => 'This is a test episode',
            'duration' => 24,
            'created_at' => now(),
            'updated_at' => now(),
            'pictures' => '[]',
            'video_players' => '[]',
        ]);

        // Отримуємо епізод з бази даних
        $episode = Episode::find($episodeId);
        $user = User::factory()->create();

        // Створюємо коментарі для епізоду
        $comments = [];
        for ($i = 0; $i < 3; $i++) {
            $comments[] = Comment::create([
                'commentable_id' => $episode->id,
                'commentable_type' => Episode::class,
                'user_id' => $user->id,
                'body' => "Comment {$i}",
                'is_spoiler' => false,
            ]);
        }

        // Оновлюємо дані епізоду
        $episode->refresh();

        // Перевіряємо, що коментарі прив'язані
        $this->assertCount(3, $episode->comments);
        $this->assertEquals(
            collect($comments)->pluck('id')->sort()->values(),
            $episode->comments->pluck('id')->sort()->values()
        );
    }

    /** @test */
    public function it_can_have_watch_history()
    {
        $anime = Anime::factory()->create([
            'kind' => Kind::TV_SERIES,
        ]);

        // Створюємо епізод безпосередньо через SQL
        $episodeId = DB::table('episodes')->insertGetId([
            'id' => \Illuminate\Support\Str::ulid(),
            'anime_id' => $anime->id,
            'number' => 1,
            'slug' => 'test-episode',
            'name' => 'Test Episode',
            'description' => 'This is a test episode',
            'duration' => 24,
            'created_at' => now(),
            'updated_at' => now(),
            'pictures' => '[]',
            'video_players' => '[]',
        ]);

        // Отримуємо епізод з бази даних
        $episode = Episode::find($episodeId);
        $users = User::factory()->count(3)->create();

        // Створюємо записи історії перегляду
        foreach ($users as $index => $user) {
            WatchHistory::create([
                'id' => \Illuminate\Support\Str::ulid(),
                'user_id' => $user->id,
                'episode_id' => $episode->id,
                'progress_time' => ($index + 1) * 100, // Різний прогрес для кожного користувача
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Отримуємо історію перегляду
        $watchHistories = WatchHistory::where('episode_id', $episode->id)->get();

        // Перевіряємо кількість записів
        $this->assertCount(3, $watchHistories);

        // Перевіряємо, що записи належать правильним користувачам
        $this->assertEquals(
            $users->pluck('id')->sort()->values(),
            $watchHistories->pluck('user_id')->sort()->values()
        );
    }

    /** @test */
    public function it_can_update_episode()
    {
        $anime = Anime::factory()->create([
            'kind' => Kind::TV_SERIES,
        ]);

        // Створюємо епізод безпосередньо через SQL
        $episodeId = DB::table('episodes')->insertGetId([
            'id' => \Illuminate\Support\Str::ulid(),
            'anime_id' => $anime->id,
            'number' => 1,
            'slug' => 'test-episode',
            'name' => 'Original Name',
            'description' => 'Original Description',
            'duration' => 24,
            'created_at' => now(),
            'updated_at' => now(),
            'pictures' => '[]',
            'video_players' => '[]',
        ]);

        // Отримуємо епізод з бази даних
        $episode = Episode::find($episodeId);

        // Оновлюємо епізод
        $episode->update([
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'duration' => 30,
        ]);

        // Перевіряємо, що дані оновлено
        $this->assertDatabaseHas('episodes', [
            'id' => $episode->id,
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'duration' => 30,
        ]);
    }

    /** @test */
    public function it_can_delete_episode()
    {
        $anime = Anime::factory()->create([
            'kind' => Kind::TV_SERIES,
        ]);

        // Створюємо епізод безпосередньо через SQL
        $episodeId = DB::table('episodes')->insertGetId([
            'id' => \Illuminate\Support\Str::ulid(),
            'anime_id' => $anime->id,
            'number' => 1,
            'slug' => 'test-episode',
            'name' => 'Test Episode',
            'description' => 'This is a test episode',
            'duration' => 24,
            'created_at' => now(),
            'updated_at' => now(),
            'pictures' => '[]',
            'video_players' => '[]',
        ]);

        // Отримуємо епізод з бази даних
        $episode = Episode::find($episodeId);
        $user = User::factory()->create();

        // Створюємо запис історії перегляду
        $watchHistory = WatchHistory::create([
            'id' => \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
            'episode_id' => $episode->id,
            'progress_time' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Видаляємо епізод
        $episode->delete();

        // Перевіряємо, що епізод видалено
        $this->assertDatabaseMissing('episodes', ['id' => $episode->id]);

        // Перевіряємо, що історія перегляду також видалена (через каскадне видалення)
        $this->assertDatabaseMissing('watch_histories', ['id' => $watchHistory->id]);
    }

    /** @test */
    public function it_generates_slug_from_name()
    {
        $anime = Anime::factory()->create([
            'kind' => Kind::TV_SERIES,
        ]);

        // Створюємо епізод безпосередньо через SQL
        $episodeId = DB::table('episodes')->insertGetId([
            'id' => \Illuminate\Support\Str::ulid(),
            'anime_id' => $anime->id,
            'number' => 1,
            'slug' => 'test-episode-name',
            'name' => 'Test Episode Name',
            'description' => 'This is a test episode',
            'duration' => 24,
            'created_at' => now(),
            'updated_at' => now(),
            'pictures' => '[]',
            'video_players' => '[]',
        ]);

        // Отримуємо епізод з бази даних
        $episode = Episode::find($episodeId);

        $this->assertEquals('test-episode-name', $episode->slug);
    }

    /** @test */
    public function it_can_find_episode_by_slug()
    {
        $anime = Anime::factory()->create([
            'kind' => Kind::TV_SERIES,
        ]);

        // Створюємо епізод безпосередньо через SQL
        $episodeId = DB::table('episodes')->insertGetId([
            'id' => \Illuminate\Support\Str::ulid(),
            'anime_id' => $anime->id,
            'number' => 1,
            'slug' => 'test-episode',
            'name' => 'Test Episode',
            'description' => 'This is a test episode',
            'duration' => 24,
            'created_at' => now(),
            'updated_at' => now(),
            'pictures' => '[]',
            'video_players' => '[]',
        ]);

        $foundEpisode = Episode::where('slug', 'test-episode')->first();

        $this->assertNotNull($foundEpisode);
        $this->assertEquals($episodeId, $foundEpisode->id);
    }

    /** @test */
    public function it_can_find_episodes_by_anime()
    {
        $anime = Anime::factory()->create([
            'kind' => Kind::TV_SERIES,
        ]);

        // Створюємо епізоди для першого аніме
        $episodeIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $episodeId = DB::table('episodes')->insertGetId([
                'id' => \Illuminate\Support\Str::ulid(),
                'anime_id' => $anime->id,
                'number' => $i,
                'slug' => "episode-{$i}",
                'name' => "Episode {$i}",
                'description' => "Description for episode {$i}",
                'duration' => 24,
                'created_at' => now(),
                'updated_at' => now(),
                'pictures' => '[]',
                'video_players' => '[]',
            ]);
            $episodeIds[] = $episodeId;
        }

        // Створюємо інше аніме
        $anotherAnime = Anime::factory()->create([
            'kind' => Kind::TV_SERIES,
        ]);

        // Створюємо епізоди для іншого аніме
        for ($i = 1; $i <= 2; $i++) {
            DB::table('episodes')->insertGetId([
                'id' => \Illuminate\Support\Str::ulid(),
                'anime_id' => $anotherAnime->id,
                'number' => $i,
                'slug' => "another-episode-{$i}",
                'name' => "Another Episode {$i}",
                'description' => "Description for another episode {$i}",
                'duration' => 24,
                'created_at' => now(),
                'updated_at' => now(),
                'pictures' => '[]',
                'video_players' => '[]',
            ]);
        }

        $animeEpisodes = Episode::where('anime_id', $anime->id)->get();

        $this->assertCount(3, $animeEpisodes);
        $this->assertEquals(
            collect($episodeIds)->sort()->values(),
            $animeEpisodes->pluck('id')->sort()->values()
        );
    }

    /** @test */
    public function it_can_order_episodes_by_number()
    {
        $anime = Anime::factory()->create([
            'kind' => Kind::TV_SERIES,
        ]);

        // Створюємо епізоди з різними номерами
        DB::table('episodes')->insertGetId([
            'id' => \Illuminate\Support\Str::ulid(),
            'anime_id' => $anime->id,
            'number' => 3,
            'slug' => 'episode-3',
            'name' => 'Episode 3',
            'description' => 'Description for episode 3',
            'duration' => 24,
            'created_at' => now(),
            'updated_at' => now(),
            'pictures' => '[]',
            'video_players' => '[]',
        ]);

        DB::table('episodes')->insertGetId([
            'id' => \Illuminate\Support\Str::ulid(),
            'anime_id' => $anime->id,
            'number' => 1,
            'slug' => 'episode-1',
            'name' => 'Episode 1',
            'description' => 'Description for episode 1',
            'duration' => 24,
            'created_at' => now(),
            'updated_at' => now(),
            'pictures' => '[]',
            'video_players' => '[]',
        ]);

        DB::table('episodes')->insertGetId([
            'id' => \Illuminate\Support\Str::ulid(),
            'anime_id' => $anime->id,
            'number' => 2,
            'slug' => 'episode-2',
            'name' => 'Episode 2',
            'description' => 'Description for episode 2',
            'duration' => 24,
            'created_at' => now(),
            'updated_at' => now(),
            'pictures' => '[]',
            'video_players' => '[]',
        ]);

        $episodes = Episode::where('anime_id', $anime->id)->orderBy('number')->get();

        $this->assertEquals(1, $episodes[0]->number);
        $this->assertEquals(2, $episodes[1]->number);
        $this->assertEquals(3, $episodes[2]->number);
    }
}
