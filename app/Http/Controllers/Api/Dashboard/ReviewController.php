<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Services\ReviewService;
use App\Services\ReviewExportService;
use App\Traits\ApiResponse;
use App\Http\Resources\Api\Dashboard\ReviewResource;
use App\Http\Requests\Api\Dashboard\AnswerReviewRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReviewController extends Controller
{
    use ApiResponse;

    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index(Request $request)
    {
        $instructorId = auth()->id();
        $perPage = $request->input('per_page', 10);

        $reviews = $this->reviewService->getInstructorReviews(
            $instructorId,
            $perPage,
            $request->has('has_comment') ? $request->boolean('has_comment') : null,
            $request->has('not_answered') ? $request->boolean('not_answered') : null,
            $request->input('sort', 'desc')
        );

        return $this->success(
            ReviewResource::collection($reviews),
            'Reviews data retrieved successfully'
        );
    }
    public function show(Review $review)
    {
        return $this->success(
            new ReviewResource($review->load(['user', 'reviewable'])),
            'Review details retrieved successfully'
        );
    }

    public function answer(AnswerReviewRequest $request, Review $review)
    {
        $review = $this->reviewService->answerReview($review, $request->answer);

        return $this->success(
            new ReviewResource($review),
            'Review answered successfully'
        );
    }

    public function export(Request $request)
    {
        $instructorId = auth()->id();
        $filters = $request->only(['has_comment', 'not_answered', 'sort']);

        return (new ReviewExportService($instructorId, $filters))
            ->export();
    }
}
