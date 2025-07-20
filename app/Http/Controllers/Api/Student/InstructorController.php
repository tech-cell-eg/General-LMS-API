<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Student\InstructorResource;
use App\Models\User;
use App\Traits\ApiResponse;

class InstructorController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $instructors = User::query()
            ->where('role', 'instructor')
            ->with(['instructorProfile'])
            ->withCount(['courses', 'enrollments'])
            ->withAvg([
                'courses as courses_avg_reviews_avg_rating' => function ($query) {
                    $query->withAvg('reviews as reviews_avg', 'rating')
                        ->selectRaw('avg(reviews_avg)');
                },
            ], 'id')
            ->latest()
            ->get();

        return $this->success(
            InstructorResource::collection($instructors),
            'Instructors retrieved successfully'
        );
    }

    public function show(User $instructor)
    {
        if (!$instructor->isInstructor()) {
            return $this->error('The specified user is not an instructor', 404);
        }

        $instructor->loadCount('courses');

        return $this->success([
            'instructor' => $instructor,
            'stats' => [
                'total_students' => $instructor->totalStudents(),
                'average_rating' => $instructor->averageRating(),
            ],
        ], 'Instructor details retrieved successfully');
    }
}
