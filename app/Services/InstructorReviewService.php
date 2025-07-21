<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class InstructorReviewService
{
    public function getRatingStats(int $instructorId): array
    {
        $stats = DB::table('reviews')
            ->join('courses', 'reviews.reviewable_id', '=', 'courses.id')
            ->where('reviews.reviewable_type', Course::class)
            ->where('courses.instructor_id', $instructorId)
            ->selectRaw('COUNT(*) as total_reviews')
            ->selectRaw('SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star')
            ->selectRaw('SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star')
            ->selectRaw('SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star')
            ->selectRaw('SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star')
            ->selectRaw('SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star')
            ->selectRaw('AVG(rating) as average_rating')
            ->first();

        return [
            'total_reviews' => $stats->total_reviews,
            'one_star' => $stats->one_star,
            'two_star' => $stats->two_star,
            'three_star' => $stats->three_star,
            'four_star' => $stats->four_star,
            'five_star' => $stats->five_star,
            'average_rating' => round($stats->average_rating, 1)
        ];
    }

public function getPaginatedReviews(int $instructorId, int $perPage = 10)
{
    return Review::with(['user', 'reviewable'])
        ->where('reviewable_type', Course::class)
        ->whereHasMorph(
            'reviewable',
            [Course::class],
            function ($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId);
            }
        )
        ->orderBy('created_at', 'desc')
        ->paginate($perPage)
        ->through(function ($review) {
            return $this->formatReview($review);
        });
}

    protected function formatReview(Review $review): array
    {
        return [
            'id' => $review->id,
            'rating' => $review->rating,
            'comment' => $review->comment,
            'course_name' => $review->reviewable->title,
            'user_full_name' => $review->user->first_name . ' ' . $review->user->last_name,
            'user_image' => $review->user->avatar_url,
            'date' => $review->created_at->format('M d, Y')
        ];
    }
}