<?php

namespace App\Policies;

use App\Http\Responses\ApiResponse;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\Response;

class WorkspacePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Workspace $workspace): bool
    {
        return $workspace->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */

    public function createTask(User $user, Workspace $workspace): bool
    {
        return $workspace->users()->where('user_id', $user->id)->exists();
    }
    public function updateTask(User $user, Workspace $workspace, Task $task): bool
    {
        return $user->id == $task->creator_id || $user->id == $workspace->owner_id;
    }
    public function changeCategory(User $user, Workspace $workspace, Task $task): bool
    {
        return $user->id == $workspace->owner_id || $user->id == $task->executor_id || $user->id == $task->creator_id;
    }

    public function createCategory(User $user, Workspace $workspace): bool
    {
        return $user->id == $workspace->owner_id;
    }
    public function updateCategory(User $user, Workspace $workspace): bool
    {
        return $user->id == $workspace->owner_id;
    }
    public function deleteCategory(User $user, Workspace $workspace): bool
    {
        return $user->id == $workspace->owner_id;
    }


    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Workspace $workspace): bool
    {
        return $user->id === $workspace->owner_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Workspace $workspace): bool
    {
        return $user->id === $workspace->owner_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Workspace $workspace): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Workspace $workspace): bool
    {
        //
    }
}
