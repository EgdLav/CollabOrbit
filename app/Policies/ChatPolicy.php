<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;

class ChatPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, Chat $chat)
    {
        return $chat->users()
            ->where('users.id', $user->id)
            ->exists();
    }
    public function message(User $user, Chat $chat)
    {
        return $chat->users()
            ->where('users.id', $user->id)
            ->exists();
    }
}
