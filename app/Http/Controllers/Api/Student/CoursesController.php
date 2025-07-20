<?php

namespace App\Http\Controllers\Api\Student;

use App\Filters\CourseFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Student\CouorseDetailsResource;
use App\Http\Resources\Api\Student\CourseCollection;
use App\Models\Course;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = Course::query()
            ->with(['instructor', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->withCount(['reviews', 'sections']);

        // Apply filters and sorting
        $filters = $request->only([
            'rate',
            'price',
            'category',
            'sections',
            'is_featured',
            'sort_by'
        ]);

        $query = (new CourseFilter)->apply($query, $filters);

        $courses = $query->paginate($perPage);

        return $this->success(
            new CourseCollection($courses),
            'Courses retrieved successfully'
        );
    }

    public function show($id)
    {
        $course = Course::with([
            'instructor',
            'instructor.instructorProfile',
            'sections.lessons',
            'reviews.user',
            'metadata',
            'enrollments',
        ])->findOrFail($id);

        return new CouorseDetailsResource($course);
    }
}
