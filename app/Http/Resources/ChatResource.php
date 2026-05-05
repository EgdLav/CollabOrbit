<?php

namespace App\Http\Resources;

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
        ];
    }
}
