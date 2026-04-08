<?php

namespace App\Services;

use App\Jobs\SendVerifyEmail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data, $avatar = null): User
    {
        if ($avatar) {
            $data['avatar'] = $avatar->store('avatars', 'public');
        }

        $user = User::create($data)->fresh();

        SendVerifyEmail::dispatch($user);

        return $user;
    }

    public function login(array $data): string|false
    {
        if (!auth()->attempt($data)) {
            return false;
        }

        return auth()->user()->createToken('auth_token')->plainTextToken;
    }
}
