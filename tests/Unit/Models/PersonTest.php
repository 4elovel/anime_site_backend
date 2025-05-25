<?php

namespace Tests\Unit\Models;

use AnimeSite\Enums\Gender;
use AnimeSite\Enums\PersonType;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Person;
use AnimeSite\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PersonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_person()
    {
        $person = Person::factory()->create([
            'name' => 'Test Person',
            'description' => 'This is a test person',
            'type' => PersonType::VOICE_ACTOR,
            'gender' => Gender::MALE,
        ]);

        $this->assertDatabaseHas('people', [
            'name' => 'Test Person',
            'description' => 'This is a test person',
            'type' => PersonType::VOICE_ACTOR->value,
            'gender' => Gender::MALE->value,
        ]);
    }

    /** @test */
    public function it_can_be_attached_to_anime()
    {
        $person = Person::factory()->create();
        $animes = Anime::factory()->count(3)->create();

        // Прив'язуємо персону до аніме з додатковими даними через SQL
        foreach ($animes as $index => $anime) {
            DB::table('anime_person')->insert([
                'anime_id' => $anime->id,
                'person_id' => $person->id,
                'character_name' => "Character {$index}",
                'voice_person_id' => null,
            ]);
        }

        // Перевіряємо, що персона прив'язана до аніме
        $this->assertCount(3, $person->animes);
        $this->assertEquals(
            $animes->pluck('id')->sort()->values(),
            $person->animes->pluck('id')->sort()->values()
        );

        // Перевіряємо додаткові дані
        $this->assertEquals('Character 0', $person->animes->first()->pivot->character_name);
    }

    /** @test */
    public function it_can_have_tags()
    {
        $person = Person::factory()->create();
        $tags = Tag::factory()->count(3)->create();

        // Прив'язуємо теги до персони
        $person->tags()->attach($tags->pluck('id')->toArray());

        // Перевіряємо, що теги прив'язані
        $this->assertCount(3, $person->tags);
        $this->assertEquals(
            $tags->pluck('id')->sort()->values(),
            $person->tags->pluck('id')->sort()->values()
        );
    }

    /** @test */
    public function it_can_update_person()
    {
        $person = Person::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original Description',
            'type' => PersonType::VOICE_ACTOR,
        ]);

        // Оновлюємо персону
        $person->update([
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'type' => PersonType::DIRECTOR,
        ]);

        // Перевіряємо, що дані оновлено
        $this->assertDatabaseHas('people', [
            'id' => $person->id,
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'type' => PersonType::DIRECTOR->value,
        ]);
    }

    /** @test */
    public function it_can_delete_person()
    {
        $person = Person::factory()->create();
        $anime = Anime::factory()->create();

        // Прив'язуємо персону до аніме через SQL
        DB::table('anime_person')->insert([
            'anime_id' => $anime->id,
            'person_id' => $person->id,
            'character_name' => 'Character Name',
            'voice_person_id' => null,
        ]);

        // Перевіряємо, що зв'язок створено
        $this->assertDatabaseHas('anime_person', [
            'anime_id' => $anime->id,
            'person_id' => $person->id,
        ]);

        // Видаляємо персону
        $person->delete();

        // Перевіряємо, що персону видалено
        $this->assertDatabaseMissing('people', ['id' => $person->id]);

        // Перевіряємо, що зв'язок також видалено
        $this->assertDatabaseMissing('anime_person', [
            'person_id' => $person->id,
        ]);
    }

    /** @test */
    public function it_generates_slug_from_name()
    {
        $person = Person::factory()->create([
            'name' => 'Test Person Name',
            'slug' => 'test-person-name',
        ]);

        $this->assertEquals('test-person-name', $person->slug);
    }

    /** @test */
    public function it_can_find_person_by_slug()
    {
        $person = Person::factory()->create([
            'name' => 'Test Person',
            'slug' => 'test-person',
        ]);

        $foundPerson = Person::where('slug', 'test-person')->first();

        $this->assertNotNull($foundPerson);
        $this->assertEquals($person->id, $foundPerson->id);
    }

    /** @test */
    public function it_can_filter_by_type()
    {
        // Створюємо персон різних типів
        Person::factory()->count(2)->create(['type' => PersonType::VOICE_ACTOR]);
        Person::factory()->count(3)->create(['type' => PersonType::DIRECTOR]);
        Person::factory()->count(1)->create(['type' => PersonType::PRODUCER]);

        // Отримуємо персон за типом
        $actors = Person::where('type', PersonType::VOICE_ACTOR->value)->get();
        $directors = Person::where('type', PersonType::DIRECTOR->value)->get();
        $producers = Person::where('type', PersonType::PRODUCER->value)->get();

        // Перевіряємо кількість
        $this->assertCount(2, $actors);
        $this->assertCount(3, $directors);
        $this->assertCount(1, $producers);
    }

    /** @test */
    public function it_can_filter_by_gender()
    {
        // Створюємо персон різних статей
        Person::factory()->count(2)->create(['gender' => Gender::MALE]);
        Person::factory()->count(3)->create(['gender' => Gender::FEMALE]);

        // Отримуємо персон за статтю
        $males = Person::where('gender', Gender::MALE->value)->get();
        $females = Person::where('gender', Gender::FEMALE->value)->get();

        // Перевіряємо кількість
        $this->assertCount(2, $males);
        $this->assertCount(3, $females);
    }
}
