<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Review;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    use ApiResponse;

    // Get my reviews
    public function myReviews()
    {
        $reviews = Review::where('user_id', Auth::id())
            ->with(['reviewable' => function ($query) {
                $query->morphWith([
                    Course::class => ['instructor'],
                ]);
            }])
            ->latest()
            ->paginate(10);

        return $this->success([
            'reviews' => $reviews,
        ], 'My reviews retrieved successfully');
    }
}
