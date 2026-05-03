<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    protected $model = Invitation::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'invitee_id' => User::factory(),
        ];
    }

    /**
     * State: pending invitation for specific workspace/user
     */
    public function forWorkspaceUser($workspace, $user)
    {
        return $this->state(fn () => [
            'workspace_id' => $workspace->id,
            'invitee_id' => $user->id,
        ]);
    }
}
