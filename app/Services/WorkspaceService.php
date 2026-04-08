<?php

namespace App\Services;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\AuthorizationException;

class WorkspaceService
{
    public function create(array $data, User $user): Workspace {
        $data['owner_id'] = $user->id;
        $workspace = Workspace::create($data);
        $workspace->users()->attach($user);
        return $workspace;
    }

    public function update(array $data, Workspace $workspace, User $user): Workspace|null {
        $filteredData = array_filter($data, fn($v) => $v !== null);

        $workspace->update($filteredData);
        return $workspace->fresh();
    }
    public function delete(Workspace $workspace): bool {
        $workspace->delete();
        return true;
    }
}
