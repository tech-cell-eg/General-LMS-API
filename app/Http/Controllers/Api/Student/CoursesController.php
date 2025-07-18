<?php

namespace App\Http\Controllers\Api\Student;

use App\Filters\CourseFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Student\CouorseDetailsResource;
use App\Http\Resources\Api\Student\CourseResource;
use App\Models\Course;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Resources\Api\Student\CourseCollection;

class CoursesController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Default to 10 items per page

        $query = Course::query()
            ->with(['instructor', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->withCount(['reviews', 'sections']);

        // Apply filters - only the requested ones
        $filters = $request->only([
            'rate',          // Filter by minimum rating
            'price',         // Filter by maximum price
            'category',     // Filter by category ID
            'sections',       // Filter by minimum number of sections
            'is_featured' // Filter by featured status
        ]);

        $query = (new CourseFilter())->apply($query, $filters);

        // Apply sorting
        $sortBy = $request->input('sort_by', 'latest'); // Default sorting
        $query = $this->applySorting($query, $sortBy);

        $courses = $query->paginate($perPage);

        return $this->success(
            new CourseCollection($courses),
            'Courses retrieved successfully'
        );
    }

    protected function applySorting($query, $sortBy)
    {
        switch ($sortBy) {
            case 'highest_rated':
                return $query->orderBy('reviews_avg_rating', 'desc');
            case 'most_reviews':
                return $query->orderBy('reviews_count', 'desc');
            case 'price_low':
                return $query->orderBy('price', 'asc');
            case 'price_high':
                return $query->orderBy('price', 'desc');
            case 'latest':
            default:
                return $query->latest();
        }
    }


    public function show($id)
    {
        $course = Course::with([
            'instructor',
            'instructor.instructorProfile',
            'sections.lessons',
            'reviews.user',
            'metadata',
            'enrollments'
        ])->findOrFail($id);

        return new CouorseDetailsResource($course);
    }
}