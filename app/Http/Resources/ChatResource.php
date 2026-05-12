<?php

namespace App\Http\Resources;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
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
            'type' => $this->type,
            'users' => UserResource::collection(
                $this->whenLoaded('users')
            ),
            'other_user' => $this->when(
                $this->type === 'private',
                function () {
                    return new UserResource(
                        $this->users
                            ->where('id', '!=', auth()->id())
                            ->first()
                    );
                }
            ),
            'last_message' => new MessageResource(
                $this->whenLoaded('lastMessage')
            ),
            'workspace' => $this->when(
                $this->type === 'workspace',
                function () {
                    return new WorkspaceResource(Workspace::find($this->workspace_id));
                }
            ),
            'messages_count' => $this->messages_count,
        ];
    }
}
