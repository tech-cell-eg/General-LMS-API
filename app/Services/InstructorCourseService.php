<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseMetadata;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InstructorCourseService
{
    public function getInstructorCourses(int $instructorId)
    {
        return Course::withCount([
                'sections',
                'orderItems as orders_count',
                'enrollments',
                'reviews',
                'cartItems as shelf_count'
            ])
            ->where('instructor_id', $instructorId)
            ->get()
            ->map(function ($course) {
                return $this->formatCourseListing($course);
            });
    }

    public function getCourseDetails(int $instructorId, int $courseId)
    {
        $course = Course::with([
                'category',
                'metadata',
                'sections' => function ($query) {
                    $query->orderBy('order');
                },
                'sections.lessons' => function ($query) {
                    $query->orderBy('order');
                },
                'sections.lessons.resources'
            ])
            ->where('instructor_id', $instructorId)
            ->findOrFail($courseId);

        return $this->formatCourseDetails($course);
    }

    public function createCourse(int $instructorId, array $data)
    {
        $data = $this->handleFileUploads($data);

        $course = Course::create([
            'instructor_id' => $instructorId,
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'short_description' => $data['short_description'],
            'full_description' => $data['full_description'],
            'price' => $data['price'],
            'discount_price' => $data['discount_price'] ?? null,
            'thumbnail_url' => $data['thumbnail_path'] ?? null,
            'preview_video_url' => $data['intro_video_path'] ?? null,
            'is_featured' => $data['is_featured'] ?? false,
            'certification_available' => $data['certification_available'] ?? false,
            'difficulty_level' => $data['difficulty_level'],
            'total_hours' => $data['total_hours'] ?? 0,
            'total_lectures' => $data['total_lectures'] ?? 0,
            'status' => 'draft',
        ]);

        CourseMetadata::create([
            'course_id' => $course->id,
            'languages' => $data['languages'] ?? null,
            'prerequisites' => $data['prerequisites'] ?? null,
            'learning_outcomes' => $data['learning_outcomes'] ?? null,
            'target_audience' => $data['target_audience'] ?? null,
        ]);

        return $course;
    }

    public function updateCourse(int $instructorId, int $courseId, array $data)
    {
        $course = Course::where('instructor_id', $instructorId)
            ->findOrFail($courseId);

        $data = $this->handleFileUploads($data, $course);

        $updateData = [
            'title' => $data['title'] ?? $course->title,
            'slug' => isset($data['title']) ? Str::slug($data['title']) : $course->slug,
            'short_description' => $data['short_description'] ?? $course->short_description,
            'full_description' => $data['full_description'] ?? $course->full_description,
            'price' => $data['price'] ?? $course->price,
            'discount_price' => $data['discount_price'] ?? $course->discount_price,
            'thumbnail_url' => $data['thumbnail_path'] ?? $course->thumbnail_url,
            'preview_video_url' => $data['intro_video_path'] ?? $course->preview_video_url,
            'category_id' => $data['category_id'] ?? $course->category_id,
            'is_featured' => $data['is_featured'] ?? $course->is_featured,
            'certification_available' => $data['certification_available'] ?? $course->certification_available,
            'difficulty_level' => $data['difficulty_level'] ?? $course->difficulty_level,
            'total_hours' => $data['total_hours'] ?? $course->total_hours,
            'total_lectures' => $data['total_lectures'] ?? $course->total_lectures,
            'status' => $data['status'] ?? $course->status,
        ];

        $course->update($updateData);

        $this->updateCourseMetadata($course, $data);

        return $course;
    }

    public function deleteCourse(int $instructorId, int $courseId)
    {
        $course = Course::where('instructor_id', $instructorId)
            ->findOrFail($courseId);

        $this->deleteCourseFiles($course);
        $course->delete();
    }

    public function publishCourse(int $instructorId, int $courseId)
    {
        $course = Course::where('instructor_id', $instructorId)
            ->findOrFail($courseId);

        $course->update([
            'status' => 'published',
            'published_at' => now()
        ]);

        return $course;
    }

    public function draftCourse(int $instructorId, int $courseId)
    {
        $course = Course::where('instructor_id', $instructorId)
            ->findOrFail($courseId);

        $course->update([
            'status' => 'draft'
        ]);

        return $course;
    }

    protected function handleFileUploads(array $data, ?Course $existingCourse = null): array
    {
        if (isset($data['intro_video'])) {
            $data['intro_video_path'] = $data['intro_video']->store('courses/intro_videos', 'public');
            if ($existingCourse && $existingCourse->preview_video_url) {
                Storage::disk('public')->delete($existingCourse->preview_video_url);
            }
        }

        if (isset($data['thumbnail'])) {
            $data['thumbnail_path'] = $data['thumbnail']->store('courses/thumbnails', 'public');
            if ($existingCourse && $existingCourse->thumbnail_url) {
                Storage::disk('public')->delete($existingCourse->thumbnail_url);
            }
        }

        return $data;
    }

    protected function deleteCourseFiles(Course $course): void
    {
        if ($course->preview_video_url) {
            Storage::disk('public')->delete($course->preview_video_url);
        }
        if ($course->thumbnail_url) {
            Storage::disk('public')->delete($course->thumbnail_url);
        }
    }

    protected function updateCourseMetadata(Course $course, array $data): void
    {
        if ($course->metadata) {
            $course->metadata->update([
                'languages' => $data['languages'] ?? $course->metadata->languages,
                'prerequisites' => $data['prerequisites'] ?? $course->metadata->prerequisites,
                'learning_outcomes' => $data['learning_outcomes'] ?? $course->metadata->learning_outcomes,
                'target_audience' => $data['target_audience'] ?? $course->metadata->target_audience,
            ]);
        } else {
            CourseMetadata::create([
                'course_id' => $course->id,
                'languages' => $data['languages'] ?? null,
                'prerequisites' => $data['prerequisites'] ?? null,
                'learning_outcomes' => $data['learning_outcomes'] ?? null,
                'target_audience' => $data['target_audience'] ?? null,
            ]);
        }
    }

    protected function formatCourseListing(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->title,
            'price' => $course->price,
            'discount_price' => $course->discount_price,
            'total_chapters' => $course->sections_count,
            'total_orders' => $course->orders_count,
            'total_certificates' => $course->certificates_count,
            'total_reviews' => $course->reviews_count,
            'total_shelves' => $course->shelf_count,
            'thumbnail_url' => $course->thumbnail_url,
            'created_at' => $course->created_at,
            'updated_at' => $course->updated_at
        ];
    }

    protected function formatCourseDetails(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->title,
            'slug' => $course->slug,
            'short_description' => $course->short_description,
            'full_description' => $course->full_description,
            'price' => $course->price,
            'discount_price' => $course->discount_price,
            'thumbnail_url' => $course->thumbnail_url ? asset('storage/' . $course->thumbnail_url) : null,
            'preview_video_url' => $course->preview_video_url ? asset('storage/' . $course->preview_video_url) : null,
            'is_featured' => $course->is_featured,
            'certification_available' => $course->certification_available,
            'difficulty_level' => $course->difficulty_level,
            'total_hours' => $course->total_hours,
            'total_lectures' => $course->total_lectures,
            'status' => $course->status,
            'created_at' => $course->created_at,
            'updated_at' => $course->updated_at,
            'category' => $course->category,
            'metadata' => $course->metadata,
            'sections' => $course->sections->map(function ($section) {
                return [
                    'id' => $section->id,
                    'title' => $section->title,
                    'order' => $section->order,
                    'description' => $section->description,
                    'status' => $section->status,
                    'lessons' => $section->lessons->map(function ($lesson) {
                        return [
                            'id' => $lesson->id,
                            'title' => $lesson->title,
                            'duration_minutes' => $lesson->duration_minutes,
                            'content_type' => $lesson->content_type,
                            'content_url' => $lesson->content_url,
                            'preview_available' => $lesson->preview_available,
                            'order' => $lesson->order,
                            'resources' => $lesson->resources
                        ];
                    })
                ];
            })
        ];
    }
}
