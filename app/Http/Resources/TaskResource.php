<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'status' => $this->status,
            'workspace_id' => $this->workspace_id,
            'executor_id' => $this->executor_id,
        ];
    }
}
