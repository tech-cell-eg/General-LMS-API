<?php
namespace App\Http\Resources\Api\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'avatar_url' => $this->avatar_url,
            'role' => $this->role,
            'profile' => $this->whenLoaded('profile', function () {
                return [
                    'id' => $this->profile->id,
                    'user_id' => $this->profile->user_id,
                    'headline' => $this->profile->headline,
                    'bio' => $this->profile->bio,
                    'language_preferences' => $this->profile->language_preferences,
                    // 'social_links' => $this->profile->social_links,
                    'created_at' => $this->profile->created_at,
                    'updated_at' => $this->profile->updated_at,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'instructor' => $this->whenLoaded('instructor', function () {
                $averageRating = $this->resource->averageRating();
                $total_students = $this->resource->totalStudents();

                return [
                    'title' => $this->instructor->title,
                    'areas_of_expertise' => $this->instructor->areas_of_expertise,
                    'professional_experience' => $this->instructor->professional_experience,
                    'links' => $this->instructor->links,
                    'total_students' => $total_students ? $total_students : 0,
                    'total_reviews' => $this->instructor->total_reviews,
                    'average_rating' => $averageRating ? number_format($averageRating, 2) : '0.00',
                ];
            }),
        ];
    }
}
