<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Student\CourseResource;
use App\Models\User;
use App\Traits\ApiResponse;

class InstructorCoursesController extends Controller
{
    use ApiResponse;

    public function __invoke(User $instructor)
    {
        if (!$instructor->isInstructor()) {
            return $this->error('The specified user is not an instructor', 404);
        }

        $courses = $instructor->courses()
            ->with(['category'])
            ->paginate(10);

        return $this->success([
            'courses' => CourseResource::collection($courses),
            'instructor' => $instructor->only(['id', 'name', 'avatar']),
        ], 'Instructor courses retrieved successfully');
    }
}
