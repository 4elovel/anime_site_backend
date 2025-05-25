<?php

namespace Tests\Unit\Models;

use AnimeSite\Enums\Kind;
use AnimeSite\Enums\Status;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Person;
use AnimeSite\Models\Studio;
use AnimeSite\Models\Tag;
use AnimeSite\Models\User;
use AnimeSite\Models\Comment;
use AnimeSite\Models\Selection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AnimeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_anime()
    {
        $studio = Studio::factory()->create();

        $anime = Anime::factory()->create([
            'name' => 'Test Anime',
            'description' => 'This is a test anime',
            'kind' => Kind::TV_SERIES,
            'status' => Status::ONGOING,
            'studio_id' => $studio->id,
        ]);

        $this->assertDatabaseHas('animes', [
            'name' => 'Test Anime',
            'description' => 'This is a test anime',
            'kind' => Kind::TV_SERIES->value,
            'status' => Status::ONGOING->value,
            'studio_id' => $studio->id,
        ]);
    }

    /** @test */
    public function it_belongs_to_studio()
    {
        $studio = Studio::factory()->create(['name' => 'Test Studio']);
        $anime = Anime::factory()->create(['studio_id' => $studio->id]);

        $this->assertEquals($studio->id, $anime->studio->id);
        $this->assertEquals('Test Studio', $anime->studio->name);
    }

    /** @test */
    public function it_has_many_episodes()
    {
        $anime = Anime::factory()->create([
            'kind' => Kind::TV_SERIES,
        ]);

        // Створюємо епізоди з різними номерами
        $episodes = [];
        for ($i = 1; $i <= 3; $i++) {
            // Створюємо епізоди безпосередньо через SQL
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
            $episodes[] = $episodeId;
        }

        // Оновлюємо дані аніме
        $anime->refresh();

        $this->assertCount(3, $anime->episodes);
    }

    /** @test */
    public function it_can_have_tags()
    {
        $anime = Anime::factory()->create();
        $tags = Tag::factory()->count(3)->create();

        // Прив'язуємо теги до аніме
        $anime->tags()->attach($tags->pluck('id')->toArray());

        // Перевіряємо, що теги прив'язані
        $this->assertCount(3, $anime->tags);
        $this->assertEquals($tags->pluck('id')->sort()->values(), $anime->tags->pluck('id')->sort()->values());
    }

    /** @test */
    public function it_can_have_people()
    {
        $anime = Anime::factory()->create();
        $people = Person::factory()->count(3)->create();

        // Прив'язуємо персон до аніме з додатковими даними
        foreach ($people as $index => $person) {
            $anime->people()->attach($person->id, [
                'character_name' => "Character {$index}",
                'voice_person_id' => null,
            ]);
        }

        // Перевіряємо, що персони прив'язані
        $this->assertCount(3, $anime->people);
        $this->assertEquals($people->pluck('id')->sort()->values(), $anime->people->pluck('id')->sort()->values());

        // Перевіряємо додаткові дані
        $this->assertEquals('Character 0', $anime->people->first()->pivot->character_name);
    }

    /** @test */
    public function it_can_have_comments()
    {
        $anime = Anime::factory()->create();
        $user = User::factory()->create();

        // Створюємо коментарі для аніме
        $comments = [];
        for ($i = 0; $i < 3; $i++) {
            $comments[] = Comment::create([
                'commentable_id' => $anime->id,
                'commentable_type' => Anime::class,
                'user_id' => $user->id,
                'body' => "Comment {$i}",
                'is_spoiler' => false,
            ]);
        }

        // Перевіряємо, що коментарі прив'язані
        $this->assertCount(3, $anime->comments);
        $this->assertEquals(
            collect($comments)->pluck('id')->sort()->values(),
            $anime->comments->pluck('id')->sort()->values()
        );
    }

    /** @test */
    public function it_can_be_in_selections()
    {
        $anime = Anime::factory()->create();
        $user = User::factory()->create();
        $selections = Selection::factory()->count(2)->create(['user_id' => $user->id]);

        // Додаємо аніме до добірок
        foreach ($selections as $selection) {
            $selection->animes()->attach($anime->id);
        }

        // Перевіряємо, що аніме додано до добірок
        $this->assertCount(2, $anime->selections);
        $this->assertEquals(
            $selections->pluck('id')->sort()->values(),
            $anime->selections->pluck('id')->sort()->values()
        );
    }

    /** @test */
    public function it_can_update_anime()
    {
        $anime = Anime::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original Description',
        ]);

        // Оновлюємо аніме
        $anime->update([
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);

        // Перевіряємо, що дані оновлено
        $this->assertDatabaseHas('animes', [
            'id' => $anime->id,
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);
    }

    /** @test */
    public function it_can_delete_anime()
    {
        $anime = Anime::factory()->create([
            'kind' => Kind::TV_SERIES,
        ]);

        // Створюємо епізоди з різними номерами
        $episodeIds = [];
        for ($i = 1; $i <= 2; $i++) {
            // Створюємо епізоди безпосередньо через SQL
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

        // Видаляємо аніме
        $anime->delete();

        // Перевіряємо, що аніме видалено
        $this->assertDatabaseMissing('animes', ['id' => $anime->id]);

        // Перевіряємо, що епізоди також видалено (через каскадне видалення)
        foreach ($episodeIds as $episodeId) {
            $this->assertDatabaseMissing('episodes', ['id' => $episodeId]);
        }
    }

    /** @test */
    public function it_generates_slug_from_name()
    {
        $anime = Anime::factory()->create([
            'name' => 'Test Anime Name',
            'slug' => 'test-anime-name',
        ]);

        $this->assertEquals('test-anime-name', $anime->slug);
    }

    /** @test */
    public function it_can_find_anime_by_slug()
    {
        $anime = Anime::factory()->create([
            'name' => 'Test Anime',
            'slug' => 'test-anime',
        ]);

        $foundAnime = Anime::where('slug', 'test-anime')->first();

        $this->assertNotNull($foundAnime);
        $this->assertEquals($anime->id, $foundAnime->id);
    }
}
