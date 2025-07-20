<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class MyCourseController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $user = Auth::user();

        $courses = Course::with(['instructor', 'category'])
            ->whereHas('enrollments', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        return $this->success([
            'courses' => $courses,
            'total_courses' => $courses->count(),
        ], 'My courses retrieved successfully');
    }

    public function show(Course $course)
    {
        $course->load([
            'instructor',
            'category',
            'sections.lessons',
            'metadata',
            'reviews.user',
        ]);

        $isEnrolled = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->exists();

        return $this->success([
            'course' => $course,
            'is_enrolled' => $isEnrolled,
            'similar_courses' => app(SimilarCoursesController::class)->getSimilarCourses($course),
        ], 'Course details retrieved successfully');
    }
}
