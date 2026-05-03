<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function update(User $user, array $data): User
    {

        // handle avatar
        if (isset($data['avatar'])) {
            $data['avatar'] = $this->updateAvatar($user, $data['avatar']);
        }

        $user->update($data);

        return $user->refresh();
    }

    public function delete(User $user): void
    {
        if ($user->avatar && $user->avatar !== 'avatars/default.png') {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();
    }

    private function updateAvatar(User $user, $file): string
    {
        // delete old avatar
        if ($user->avatar && $user->avatar !== 'avatars/default.png') {
            Storage::disk('public')->delete($user->avatar);
        }

        return $file->store('avatars', 'public');
    }
}
