<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\Section;

class LessonService
{
    public function getLessonsForSection(int $sectionId): array
    {
        $lessons = Lesson::where('section_id', $sectionId)
            ->orderBy('order')
            ->get([
                'id',
                'title',
                'content_type as type',
                'created_at as date',
                'preview_available as status',
                'duration_minutes'
            ]);

        $section = Section::with('course')->find($sectionId);
        $lessons->each(function ($lesson) use ($section) {
            $lesson->price = $section->course->price;
        });

        return $lessons->toArray();
    }

    public function getLessonDetails(int $lessonId): array
    {
        $lesson = Lesson::findOrFail($lessonId, [
            'id',
            'title',
            'content_type',
            'content_url',
            'duration_minutes',
            'preview_available'
        ]);

        return [
            'id' => $lesson->id,
            'title' => $lesson->title,
            'content_type' => $lesson->content_type,
            'content_url' => $lesson->content_url,
            'duration' => $lesson->duration_minutes,
            'preview_available' => $lesson->preview_available
        ];
    }

    public function updateLesson(int $lessonId, array $validatedData): Lesson
    {
        $lesson = Lesson::findOrFail($lessonId);
        $lesson->update($validatedData);
        return $lesson;
    }
}
