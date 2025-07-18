<?php

namespace App\Http\Resources\Api\Student;

use Illuminate\Http\Resources\Json\JsonResource;

class CouorseDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        // Calculate average rating and total reviews
        $averageRating = $this->reviews->avg('rating') ?? 0;
        $totalReviews = $this->reviews->count();

        // Calculate total students (assuming this is available through enrollments)
        $totalStudents = $this->enrollments->count();

        // Get instructor metadata
        $instructor = $this->instructor;
        $instructorProfile = $instructor->instructorProfile;

        // Process sections with lessons count and duration
        $sections = $this->sections->map(function ($section) {
            $totalLessons = $section->lessons->count();
            $totalHours = $section->lessons->sum('duration_minutes') / 60;

            return [
                'section' => $section->title,
                'total_lessons' => $totalLessons,
                'total_hours' => round($totalHours, 1),
                'description' => $section->description,
                'order' => $section->order,
            ];
        });

        // Process instructor reviews
        $instructorReviews = $instructor->reviews->take(5)->map(function ($review) {
            return [
                'comment' => $review->comment,
                'rating' => $review->rating,
                'date' => $review->created_at->format('Y-m-d'),
                'reviewer' => [
                    'id' => $review->user->id,
                    'name' => $review->user->full_name,
                    'image' => $review->user->avatar_url,
                ]
            ];
        });

        // Get course metadata
        $metadata = $this->metadata;
        $languages = $metadata ? $metadata->languages : [];

        return [
            'id' => $this->id,
            'title' => $this->title,
            'short_description' => $this->short_description,
            'description' => $this->full_description,
            'total_hours' => $this->total_hours,
            'total_lectures' => $this->total_lectures,
            'level' => $this->difficulty_level,
            'total_rate' => $totalReviews,
            'average_rate' => round($averageRating, 1),
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'thumbnail_url' => $this->thumbnail_url,
            'certification_available' => (bool) $this->certification_available,
            'is_featured' => (bool) $this->is_featured,

            'instructor' => [
                'full_name' => $instructor->first_name . ' ' . $instructor->last_name,
                'image' => $instructor->avatar_url,
                'languages' => $languages,
                'total_reviews' => $instructorProfile->total_reviews ?? 0,
                'total_students' => $instructorProfile->total_students ?? 0,
                'total_courses' => $instructor->courses->count(),
                'bio' => $instructorProfile->bio ?? '',
                'expertise' => $instructorProfile->areas_of_expertise ?? [],
            ],

            'syllabus' => $sections,

            'reviews' => $this->reviews->map(function ($review) {
                return [
                    'comment' => $review->comment,
                    'rating' => $review->rating,
                    'date' => $review->created_at->format('Y-m-d'),
                    'reviewer' => [
                        'id' => $review->user->id,
                        'name' => $review->user->first_name . ' ' . $review->user->last_name,
                        'image' => $review->user->avatar_url,
                    ]
                ];
            }),

            'instructor_reviews' => $instructorReviews,

            'metadata' => [
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
                'published_at' => $this->published_at,
            ]
        ];
    }
}
