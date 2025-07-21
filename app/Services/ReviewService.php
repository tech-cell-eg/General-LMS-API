<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewService
{
public function getInstructorReviews(
    int $instructorId,
    int $perPage = 10,
    ?bool $hasComment = null,
    ?bool $notAnswered = null,
    string $sort = 'desc'
): LengthAwarePaginator {
    return Review::with(['user', 'reviewable.instructor'])
        ->whereHasMorph(
            'reviewable',
            [Course::class],
            function ($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId);
            }
        )
        ->when($hasComment !== null, function($query) use ($hasComment) {
            return $hasComment
                ? $query->whereNotNull('comment')
                : $query->whereNull('comment');
        })
        ->when($notAnswered !== null, function($query) use ($notAnswered) {
            return $notAnswered
                ? $query->whereNull('answer')
                : $query->whereNotNull('answer');
        })
        ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
        ->paginate($perPage);
}

    public function answerReview(Review $review, string $answer): Review
    {
        $review->update([
            'answer' => $answer,
            'answered_at' => now()
        ]);

        return $review->load(['user', 'reviewable.instructor']);
    }

    public function getReviewStats(int $instructorId): array
    {
        $stats = Review::whereHasMorph(
                'reviewable',
                [Course::class],
                function ($query) use ($instructorId) {
                    $query->where('instructor_id', $instructorId);
                }
            )
            ->selectRaw('COUNT(*) as total_reviews')
            ->selectRaw('SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star')
            ->selectRaw('SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star')
            ->selectRaw('SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star')
            ->selectRaw('SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star')
            ->selectRaw('SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star')
            ->selectRaw('AVG(rating) as average_rating')
            ->first();

        return [
            'total_reviews' => $stats->total_reviews ?? 0,
            'one_star' => $stats->one_star ?? 0,
            'two_star' => $stats->two_star ?? 0,
            'three_star' => $stats->three_star ?? 0,
            'four_star' => $stats->four_star ?? 0,
            'five_star' => $stats->five_star ?? 0,
            'average_rating' => round($stats->average_rating ?? 0, 1)
        ];
    }
}