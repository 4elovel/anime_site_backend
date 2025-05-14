<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use AnimeSite\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Can create a user.
     *
     * @return void
     */
    public function test_can_create_user()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Test: Can read a user.
     *
     * @return void
     */
    public function test_can_read_user()
    {
        $user = User::factory()->create();

        $foundUser = DB::table('users')->where('id', $user->id)->first();

        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }

    /**
     * Test: Can update a user.
     *
     * @return void
     */
    public function test_can_update_user()
    {
        $user = User::factory()->create();

        $user->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('users', ['name' => 'Updated Name']);
    }

    /**
     * Test: Can delete a user.
     *
     * @return void
     */
    public function test_can_delete_user()
    {
        $user = User::factory()->create();

        $user->delete();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
