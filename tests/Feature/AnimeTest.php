<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use AnimeSite\Models\Anime;

class AnimeTest extends TestCase
{


    public function test_can_create_anime()
    {
        $anime = Anime::factory()->create([
            'name' => 'Naruto',
            'description' => 'An epic ninja story.',
        ]);

        $this->assertDatabaseHas('animes', [
            'name' => 'Naruto',
            'description' => 'An epic ninja story.',
        ]);
    }


    public function test_can_read_anime()
    {
        $anime = Anime::factory()->create();

        $foundAnime = DB::table('animes')->where('id', $anime->id)->first();

        $this->assertNotNull($foundAnime);
        $this->assertEquals($anime->id, $foundAnime->id);
    }


    public function test_can_update_anime()
    {
        $anime = Anime::factory()->create();

        $anime->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('animes', ['name' => 'Updated Name']);
    }


    public function test_can_delete_anime()
    {
        $anime = Anime::factory()->create();

        $anime->delete();

        $this->assertDatabaseMissing('animes', ['id' => $anime->id]);
    }
}
