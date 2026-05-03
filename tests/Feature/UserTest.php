<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        return User::factory()->create([
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function user_can_update_their_profile()
    {
        Storage::fake('public');

        $user = $this->actingUser();

        $this->actingAs($user);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->patchJson('/api/profile', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'bio' => 'New bio',
            'department' => 'Backend Development',
            'avatar' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'first_name',
                        'last_name',
                        'avatar',
                    ],
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        Storage::disk('public')->assertExists(
            $user->fresh()->avatar
        );
    }

    /** @test */
    public function user_can_update_without_avatar()
    {
        $user = $this->actingUser();
        $this->actingAs($user);

        $response = $this->patchJson('/api/profile', [
            'first_name' => 'Updated',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Updated',
        ]);
    }

    /** @test */
    public function user_can_delete_their_account()
    {
        Storage::fake('public');

        $user = $this->actingUser();
        $this->actingAs($user);

        $response = $this->deleteJson('/api/profile');

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function guest_cannot_update_user()
    {
        $response = $this->patchJson('/api/profile', [
            'first_name' => 'Hack',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function guest_cannot_delete_user()
    {
        $response = $this->deleteJson('/api/profile');

        $response->assertStatus(401);
    }

}
