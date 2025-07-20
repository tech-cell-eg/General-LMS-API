<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\CourseRequest;
use App\Services\InstructorCourseService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class InstructorCoursesController extends Controller
{
    use ApiResponse;

    protected $courseService;

    public function __construct(InstructorCourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        $instructor = auth()->user();
        $courses = $this->courseService->getInstructorCourses($instructor->id);
        return $this->success($courses, 'Courses retrieved successfully');
    }

    public function store(CourseRequest $request)
    {
        $instructor = auth()->user();
        $course = $this->courseService->createCourse($instructor->id, $request->validated());
        return $this->success($course, 'Course created successfully', 201);
    }

    public function show($id)
    {
        $instructor = auth()->user();
        $course = $this->courseService->getCourseDetails($instructor->id, $id);
        return $this->success($course, 'Course retrieved successfully');
    }

    public function update(CourseRequest $request, $id)
    {
        $instructor = auth()->user();
        $course = $this->courseService->updateCourse($instructor->id, $id, $request->validated());
        return $this->success($course, 'Course updated successfully');
    }

    public function destroy($id)
    {
        $instructor = auth()->user();
        $this->courseService->deleteCourse($instructor->id, $id);
        return $this->success(null, 'Course deleted successfully');
    }

    public function publish($id)
    {
        $instructor = auth()->user();
        $course = $this->courseService->publishCourse($instructor->id, $id);
        return $this->success($course, 'Course published successfully');
    }

    public function draft($id)
    {
        $instructor = auth()->user();
        $course = $this->courseService->draftCourse($instructor->id, $id);
        return $this->success($course, 'Course moved to draft successfully');
    }
}
