<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\InstructorStudentService;
use App\Traits\ApiResponse;

class InstructorStudentsController extends Controller
{
    use ApiResponse;

    protected $studentService;

    public function __construct(InstructorStudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index()
    {
        $instructor = auth()->user();
        $students = $this->studentService->getStudentsForInstructor($instructor->id);

        return $this->success($students, 'Students retrieved successfully');
    }
}
