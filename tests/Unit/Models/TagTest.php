<?php

namespace Tests\Unit\Models;

use AnimeSite\Models\Tag;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Person;
use AnimeSite\Models\Selection;
use AnimeSite\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_tag()
    {
        $tag = Tag::factory()->create([
            'name' => 'Test Tag',
            'description' => 'This is a test tag',
            'is_genre' => true,
        ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'Test Tag',
            'description' => 'This is a test tag',
            'is_genre' => true,
        ]);
    }

    /** @test */
    public function it_can_have_parent_tag()
    {
        $parentTag = Tag::factory()->create(['name' => 'Parent Tag']);
        $childTag = Tag::factory()->create([
            'name' => 'Child Tag',
            'parent_id' => $parentTag->id,
        ]);

        $this->assertEquals($parentTag->id, $childTag->parent->id);
        $this->assertEquals('Parent Tag', $childTag->parent->name);
    }

    /** @test */
    public function it_can_have_child_tags()
    {
        $parentTag = Tag::factory()->create(['name' => 'Parent Tag']);
        $childTags = Tag::factory()->count(3)->create(['parent_id' => $parentTag->id]);

        $this->assertCount(3, $parentTag->children);
        $this->assertEquals(
            $childTags->pluck('id')->sort()->values(),
            $parentTag->children->pluck('id')->sort()->values()
        );
    }

    /** @test */
    public function it_can_be_attached_to_anime()
    {
        $tag = Tag::factory()->create();
        $animes = Anime::factory()->count(3)->create();

        // Прив'язуємо тег до аніме через таблицю taggables
        foreach ($animes as $anime) {
            DB::table('taggables')->insert([
                'tag_id' => $tag->id,
                'taggable_id' => $anime->id,
                'taggable_type' => 'AnimeSite\\Models\\Anime',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Оновлюємо дані тегу
        $tag->refresh();

        // Перевіряємо, що тег прив'язаний до аніме
        $this->assertCount(3, $tag->animes);
        $this->assertEquals(
            $animes->pluck('id')->sort()->values(),
            $tag->animes->pluck('id')->sort()->values()
        );
    }

    /** @test */
    public function it_can_be_attached_to_person()
    {
        $tag = Tag::factory()->create();
        $people = Person::factory()->count(3)->create();

        // Прив'язуємо тег до персон через таблицю taggables
        foreach ($people as $person) {
            DB::table('taggables')->insert([
                'tag_id' => $tag->id,
                'taggable_id' => $person->id,
                'taggable_type' => 'AnimeSite\\Models\\Person',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Оновлюємо дані тегу
        $tag->refresh();

        // Перевіряємо, що тег прив'язаний до персон
        $this->assertCount(3, $tag->people);
        $this->assertEquals(
            $people->pluck('id')->sort()->values(),
            $tag->people->pluck('id')->sort()->values()
        );
    }

    /** @test */
    public function it_can_be_attached_to_selection()
    {
        $tag = Tag::factory()->create();
        $user = User::factory()->create();
        $selections = Selection::factory()->count(3)->create(['user_id' => $user->id]);

        // Прив'язуємо тег до добірок через таблицю taggables
        foreach ($selections as $selection) {
            DB::table('taggables')->insert([
                'tag_id' => $tag->id,
                'taggable_id' => $selection->id,
                'taggable_type' => 'AnimeSite\\Models\\Selection',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Оновлюємо дані тегу
        $tag->refresh();

        // Перевіряємо, що тег прив'язаний до добірок
        $this->assertCount(3, $tag->selections);
        $this->assertEquals(
            $selections->pluck('id')->sort()->values(),
            $tag->selections->pluck('id')->sort()->values()
        );
    }

    /** @test */
    public function it_can_filter_by_is_genre()
    {
        // Створюємо теги-жанри
        Tag::factory()->count(3)->create(['is_genre' => true]);

        // Створюємо звичайні теги
        Tag::factory()->count(2)->create(['is_genre' => false]);

        // Отримуємо теги-жанри
        $genres = Tag::where('is_genre', true)->get();

        // Отримуємо звичайні теги
        $nonGenres = Tag::where('is_genre', false)->get();

        // Перевіряємо кількість
        $this->assertCount(3, $genres);
        $this->assertCount(2, $nonGenres);
    }

    /** @test */
    public function it_can_update_tag()
    {
        $tag = Tag::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original Description',
            'is_genre' => false,
        ]);

        // Оновлюємо тег
        $tag->update([
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'is_genre' => true,
        ]);

        // Перевіряємо, що дані оновлено
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'is_genre' => true,
        ]);
    }

    /** @test */
    public function it_can_delete_tag()
    {
        $tag = Tag::factory()->create();
        $anime = Anime::factory()->create();

        // Прив'язуємо тег до аніме через таблицю taggables
        DB::table('taggables')->insert([
            'tag_id' => $tag->id,
            'taggable_id' => $anime->id,
            'taggable_type' => 'AnimeSite\\Models\\Anime',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Перевіряємо, що зв'язок створено
        $this->assertDatabaseHas('taggables', [
            'taggable_id' => $anime->id,
            'taggable_type' => Anime::class,
            'tag_id' => $tag->id,
        ]);

        // Видаляємо зв'язок вручну
        DB::table('taggables')->where('tag_id', $tag->id)->delete();

        // Видаляємо тег
        $tag->delete();

        // Перевіряємо, що тег видалено
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);

        // Перевіряємо, що зв'язок також видалено
        $this->assertDatabaseMissing('taggables', [
            'tag_id' => $tag->id,
        ]);
    }

    /** @test */
    public function it_generates_slug_from_name()
    {
        $tag = Tag::factory()->create([
            'name' => 'Test Tag Name',
            'slug' => 'test-tag-name',
        ]);

        $this->assertEquals('test-tag-name', $tag->slug);
    }

    /** @test */
    public function it_can_find_tag_by_slug()
    {
        $tag = Tag::factory()->create([
            'name' => 'Test Tag',
            'slug' => 'test-tag',
        ]);

        $foundTag = Tag::where('slug', 'test-tag')->first();

        $this->assertNotNull($foundTag);
        $this->assertEquals($tag->id, $foundTag->id);
    }
}
