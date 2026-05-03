<?php

namespace App\Services;

use App\Http\Responses\ApiResponse;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Workspace;

class InvitationService
{
    public function create(Workspace $workspace, int $invitee_id): Invitation
    {
        return Invitation::create([
            'workspace_id' => $workspace->id,
            'invitee_id' => $invitee_id,
        ]);
    }

    /**
     * @param Invitation $invitation
     * @param string $status
     * @return Invitation
     * @throws \Exception
     */
    public function update(User $user, Workspace $workspace, Invitation $invitation, string $status): void
    {
        if ($status == 'accepted') {
            $workspace->users()->attach($user);
        }
        $invitation->delete();
    }
}
