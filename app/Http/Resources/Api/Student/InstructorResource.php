<?php

namespace App\Http\Resources\Api\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructorResource extends JsonResource
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
            'name' => $this->first_name . ' ' . $this->last_name,
            'title' => $this->instructorProfile->title ?? null, // Access through relationship
            'image' => $this->avatar_url ? asset('storage/' . $this->avatar_url) : null,
            'rate' => (float) ($this->courses_avg_reviews_avg_rating ?? $this->instructorProfile->average_rating ?? 0),
            'total_students' => (int) $this->enrollments_count,
            'total_courses' => (int) $this->courses_count,
        ];
    }
}
