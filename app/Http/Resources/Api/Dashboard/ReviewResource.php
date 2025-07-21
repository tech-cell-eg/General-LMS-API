<?php

namespace App\Http\Resources\Api\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'course_name' => $this->reviewable->title,
            'user_name' => $this->user->name,
            'user_image' => $this->user->avatar_url,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'answer' => $this->answer,
            'date' => $this->created_at->format('M d, Y'),
            'answered_at' => $this->answered_at?->format('M d, Y'),
            'is_answered' => !is_null($this->answer)
        ];
    }
}
