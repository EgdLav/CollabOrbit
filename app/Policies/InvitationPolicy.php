<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;
use App\Models\Workspace;

class InvitationPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function update(User $user, Invitation $invitation): bool
    {
        return $user->id == $invitation->invitee_id;
    }
}
