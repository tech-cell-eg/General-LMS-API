<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;

class SimilarCoursesController extends Controller
{
    public function getSimilarCourses(Course $course)
    {
        return Course::where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->with('instructor')
            ->limit(4)
            ->get();
    }
}
