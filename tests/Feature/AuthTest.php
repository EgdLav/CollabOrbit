<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_user_can_register(): void
    {
        $data = [
            'first_name' => 'Egor',
            'last_name' => 'Test',
            'email' => 'egor@test.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'avatar',
                    ]
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'egor@test.com',
        ]);
    }
    public function test_user_can_login(): void
    {
        // создаём юзера через фабрику
        $user = \App\Models\User::factory()->create([
            'email' => 'egor@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'egor@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                ]
            ]);
    }
    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'egor@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'egor@test.com',
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Authentication failed',
            ]);
    }

}
