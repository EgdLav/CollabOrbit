<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\Workspace;

class InvitationService
{
    public function create(Workspace $workspace, int $inviterId, int $inviteeId)
    {
        // prevent inviting existing member
        if ($workspace->users()->where('user_id', $inviteeId)->exists()) {
            throw new \Exception('User already in workspace');
        }

        return Invitation::create([
            'workspace_id' => $workspace->id,
            'invitee_id' => $inviteeId,
        ]);
    }

    /**
     * @param Invitation $invitation
     * @param string $status
     * @return Invitation
     * @throws \Exception
     */
    public function update(Invitation $invitation, string $status)
    {
        if ($invitation->status !== 'pending') {
            throw new \Exception('Invitation already handled');
        }

        $invitation->update(['status' => $status]);

        if ($status === 'accepted') {
            $invitation->workspace->users()->attach($invitation->invitee_id);
        }

        return $invitation;
    }
}
