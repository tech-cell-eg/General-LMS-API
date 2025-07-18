<?php

namespace App\Http\Resources\Api\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'title' => $this->title,
            'instructor' => $this->instructor->first_name.' '.$this->instructor->last_name,
            'rate' => (float) $this->reviews_avg_rating,
            'total_rating' => (int) $this->reviews_count,
            'total_hours' => $this->total_hours,
            'total_lectures' => $this->total_lectures,
            'level' => $this->difficulty_level,
            'price' => $this->price,
            'image' => $this->image_url,
        ];
    }
}
