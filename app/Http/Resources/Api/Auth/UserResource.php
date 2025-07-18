<?php

namespace App\Http\Resources\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'avatar_url' => $this->avatar_url ? asset('storage/' . $this->avatar_url) : null,
            'bio' => $this->profile ? $this->profile->bio : null,
            'website' => $this->profile ? $this->profile->website : null,
            'social_links' => $this->profile ? $this->profile->social_links : null,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
