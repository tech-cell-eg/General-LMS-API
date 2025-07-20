<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\InstructorReviewService;
use App\Traits\ApiResponse;

class InstructorReviewesController extends Controller
{
    use ApiResponse;

    protected $reviewService;

    public function __construct(InstructorReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index()
    {
        $instructor = auth()->user();
        $stats = $this->reviewService->getRatingStats($instructor->id);
        $reviews = $this->reviewService->getPaginatedReviews($instructor->id);

        return $this->success([
            'stats' => $stats,
            'reviews' => $reviews
        ], 'Reviews data retrieved successfully');
    }
}
