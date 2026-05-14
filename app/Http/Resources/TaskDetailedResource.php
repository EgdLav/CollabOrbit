<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskDetailedResource extends JsonResource
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
            'preview' => $this->preview_url,
            'files' => $this->files_urls,
            'due_date' => $this->due_date,
            'workspace' => new WorkspaceBriefResource($this->workspace),
            'category' => new CategoryBriefResource($this->category),
            'creator' => new UserResource($this->creator),
            'executor' => new UserResource($this->executor),
            'creator_id' => $this->creator_id,
            'executor_id'=> $this->executor_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
