<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'avatar' => $this->avatar_url,
            'email' => $this->email,
            'bio' => $this->bio,
            'department' => $this->department,
            'profile_created' => Carbon::make($this->created_at)->format('d-m-Y'),
        ];
    }
}
