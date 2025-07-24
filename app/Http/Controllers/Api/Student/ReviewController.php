<?php

namespace App\Http\Controllers\Api\Student;

use App\Models\Course;
use App\Models\Review;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    use ApiResponse;

    // Get my reviews
    public function index()
    {
        $reviews = Review::where('user_id', Auth::id())
            ->with([
                'reviewable' => function ($query) {
                    $query->morphWith([
                        Course::class => ['instructor'],
                    ]);
                }
            ])
            ->latest()
            ->paginate(10);

        return $this->success([
            'reviews' => $reviews,
        ], 'My reviews retrieved successfully');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instructor_id' => 'required|exists:users,id',
            'rating' => 'required|integer|between:1,5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 422);
        }

        // Check if user already reviewed this instructor
        $existingReview = Review::where('user_id', Auth::id())
            ->where('reviewable_type', User::class)
            ->where('reviewable_id', $request->instructor_id)
            ->first();

        if ($existingReview) {
            return $this->error('You have already reviewed this instructor', 400);
        }

        $review = Review::create([
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'reviewable_type' => User::class,
            'reviewable_id' => $request->instructor_id,
        ]);

        return $this->success([
            'review' => $review,
        ], 'Instructor review added successfully', 201);
    }
}