<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\UpdateSectionRequest;
use App\Services\SectionService;
use App\Traits\ApiResponse;

class SectionController extends Controller
{
    use ApiResponse;

    protected $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function index($courseId)
    {
        $sections = $this->sectionService->getSectionsForCourse($courseId);
        return $this->success($sections, 'Sections retrieved successfully');
    }

    public function show($sectionId)
    {
        $section = $this->sectionService->getSectionDetails($sectionId);
        return $this->success($section, 'Section details retrieved successfully');
    }

    public function update(UpdateSectionRequest $request, $sectionId)
    {
        $section = $this->sectionService->updateSection($sectionId, $request->validated());
        return $this->success($section, 'Section updated successfully');
    }
}
