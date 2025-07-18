<?php

namespace App\Http\Resources\Api\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
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
            'name' => $this->user->first_name.' '.$this->user->last_name,
            'title' => $this->user->role,
            'image' => $this->user->avatar_url ? asset('storage/'.$this->user->avatar_url) : null,
            'content' => $this->content,
        ];
    }
}
