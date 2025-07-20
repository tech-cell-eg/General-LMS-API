<?php

namespace App\Services;

use App\Models\Section;

class SectionService
{
    public function getSectionsForCourse(int $courseId): array
    {
        return Section::with('course')
            ->where('course_id', $courseId)
            ->orderBy('order')
            ->get(['id', 'course_id', 'title', 'order as type', 'created_at as date', 'status'])
            ->map(function ($section) {
                $section->price = optional($section->course)->price ?? 0;
                return $section;
            })
            ->filter()
            ->values()
            ->toArray();
    }

    public function getSectionDetails(int $sectionId): array
    {
        $section = Section::findOrFail($sectionId, ['id', 'title', 'description']);

        return [
            'id' => $section->id,
            'title' => $section->title,
            'subtitle' => $section->subtitle ?? '',
            'description' => $section->description
        ];
    }

    public function updateSection(int $sectionId, array $validatedData): Section
    {
        $section = Section::findOrFail($sectionId);
        $section->update($validatedData);
        return $section;
    }
}
