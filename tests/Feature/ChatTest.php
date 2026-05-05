<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    /** @test */

    public function test_user_can_get_chats_list()
    {
        $user = User::factory()->create();

        $chat = Chat::factory()->create();
        $chat->users()->attach($user->id);

        $this->actingAs($user)
            ->getJson('/api/chats')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'chats'
                ]
            ]);
    }
    public function test_private_chat_is_created()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)
            ->postJson("/api/chats/private/{$user2->id}");

        $response->assertOk();

        $this->assertDatabaseHas('chats', [
            'type' => 'private',
        ]);

        $chat = Chat::first();

        $this->assertTrue(
            $chat->users->contains($user1->id) &&
            $chat->users->contains($user2->id)
        );
    }
    public function test_private_chat_is_not_duplicated()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // first call
        $this->actingAs($user1)
            ->postJson("/api/chats/private/{$user2->id}");

        // second call
        $this->actingAs($user1)
            ->postJson("/api/chats/private/{$user2->id}");

        $this->assertEquals(1, Chat::count());
    }

//    public function user_can_view_chat()
//    {
//        $this->withoutExceptionHandling();
//        $user = User::factory()->create();
//        $other = User::factory()->create();
//
//        $chat = Chat::factory()
//            ->create();
//        $chat->users()->attach([$user->id, $other->id]);
//
//        $this->actingAs($user, 'sanctum')
//            ->getJson("/api/chats/{$other->id}")
//            ->assertOk()
//            ->assertJsonStructure([
//                'data' => [
//                    'chat' => [
//                        'id',
//                        'type',
//                        'users',
//                        'other_user',
//                        'last_message',
//                    ]
//                ]
//            ]);
//    }
//
//    /** @test */
//    public function user_can_send_message()
//    {
//        $this->withoutExceptionHandling();
//        $user = User::factory()->create();
//        $receiver = User::factory()->create();
//
//        $this->actingAs($user)
//            ->postJson("/api/chats/{$receiver->id}/messages", [
//                'body' => 'Hello world',
//            ])
//            ->assertCreated()
//            ->assertJsonStructure([
//                'data' => [
//                    'message' => [
//                        'id',
//                        'body',
//                        'created_at',
//                        'time',
//                        'is_mine',
//                        'user',
//                    ]
//                ]
//            ]);
//
//        $this->assertDatabaseHas('messages', [
//            'body' => 'Hello world',
//            'user_id' => $user->id,
//        ]);
//    }
//
//    /** @test */
//    public function user_can_get_messages_list()
//    {
//        $user = User::factory()->create();
//        $other = User::factory()->create();
//
//        $chat = Chat::factory()->create();
//        $chat->users()->attach([$user->id, $other->id]);
//
//        Message::factory()->count(5)->create([
//            'chat_id' => $chat->id,
//            'user_id' => $user->id,
//        ]);
//
//        $this->actingAs($user)
//            ->getJson("/api/chats/{$other->id}/messages")
//            ->assertOk()
//            ->assertJsonStructure([
//                'data' => [
//                    'messages'
//                ]
//            ]);
//    }
//
//    /** @test */
//    public function unauthorized_user_cannot_view_chat()
//    {
//        $user = User::factory()->create();
//        $other = User::factory()->create();
//
//        $chat = Chat::factory()->create();
//
//        // no attach → not member
//
//        $this->actingAs($user)
//            ->getJson("/api/chats/{$other->id}")
//            ->assertForbidden();
//    }
}
