<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReviewExportService
{
    protected $instructorId;
    protected $filters;

    public function __construct(int $instructorId, array $filters = [])
    {
        $this->instructorId = $instructorId;
        $this->filters = $filters;
    }

    public function export(): StreamedResponse
    {
        $fileName = 'reviews-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($handle, [
                'Course Name',
                'User Name',
                'User Image',
                'Rating',
                'Comment',
                'Answer',
                'Date',
                'Answered At'
            ]);

            // Build the base query
            $query = Review::with([
                    'user',
                    'reviewable' => function($query) {
                        $query->with('instructor');
                    }
                ])
                ->where('reviewable_type', Course::class)
                ->whereHas('reviewable', function($query) {
                    $query->where('instructor_id', $this->instructorId);
                });

            // Apply filters
            if (isset($this->filters['has_comment'])) {
                $query->whereNotNull('comment');
            }

            if (isset($this->filters['not_answered'])) {
                $query->whereNull('answer');
            }

            // Apply sorting
            $sort = $this->filters['sort'] ?? 'desc';
            $query->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc');

            // Process in chunks
            $query->chunk(200, function ($reviews) use ($handle) {
                foreach ($reviews as $review) {
                    fputcsv($handle, [
                        $review->reviewable->title ?? '',
                        ($review->user->first_name ?? '') . ' ' . ($review->user->last_name ?? ''),
                        $review->user->avatar_url ?? '',
                        $review->rating,
                        $review->comment,
                        $review->answer,
                        $review->created_at->format('Y-m-d H:i:s'),
                        $review->answered_at?->format('Y-m-d H:i:s') ?? ''
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
