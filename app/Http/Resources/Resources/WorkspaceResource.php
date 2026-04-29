<?php

namespace App\Http\Resources\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'owner' => new UserResource($this->owner),
            'is_owner' => $request->user()?->id === $this->owner_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'members' => UserResource::collection($this->users),
            'categories' => CategoryResource::collection($this->categories),
        ];
    }
}
