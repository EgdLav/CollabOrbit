<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    protected $model = Chat::class;

    public function definition(): array
    {
        return [
            'type' => 'private', // or 'group' if you have it
        ];
    }

    public function withUsers($users = null)
    {
        return $this->afterCreating(function (Chat $chat) use ($users) {
            if (!$users) {
                $users = \App\Models\User::factory()->count(2)->create();
            }

            $chat->users()->attach(
                collect($users)->pluck('id')->toArray()
            );
        });
    }

    public function withMessages($count = 3)
    {
        return $this->afterCreating(function (Chat $chat) use ($count) {
            $users = $chat->users;

            Message::factory()
                ->count($count)
                ->create([
                    'chat_id' => $chat->id,
                    'user_id' => $users->random()->id,
                ]);
        });
    }
}
