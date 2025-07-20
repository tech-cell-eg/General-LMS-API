<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\UpdateLessonRequest;
use App\Services\LessonService;
use App\Traits\ApiResponse;

class LessonController extends Controller
{
    use ApiResponse;

    protected $lessonService;

    public function __construct(LessonService $lessonService)
    {
        $this->lessonService = $lessonService;
    }

    public function index($sectionId)
    {
        $lessons = $this->lessonService->getLessonsForSection($sectionId);
        return $this->success($lessons, 'Lessons retrieved successfully');
    }

    public function show($lessonId)
    {
        $lesson = $this->lessonService->getLessonDetails($lessonId);
        return $this->success($lesson, 'Lesson details retrieved successfully');
    }

    public function update(UpdateLessonRequest $request, $lessonId)
    {
        $lesson = $this->lessonService->updateLesson($lessonId, $request->validated());
        return $this->success($lesson, 'Lesson updated successfully');
    }
}
