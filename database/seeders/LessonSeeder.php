<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LessonSeeder extends Seeder
{
    public function run()
    {
        $sections = Section::all();

        foreach ($sections as $section) {
            $lessonCount = rand(3, 10);

            for ($i = 1; $i <= $lessonCount; $i++) {
                $contentTypes = ['video', 'article', 'quiz'];
                $type = $contentTypes[array_rand($contentTypes)];

                Lesson::create([
                    'section_id' => $section->id,
                    'title' => $this->getLessonTitle($i, $type),
                    'duration_minutes' => rand(5, 45),
                    'content_type' => $type,
                    'content_url' => $this->getContentUrl($type),
                    'preview_available' => $i === 1 && $type === 'video',
                    'order' => $i,
                ]);
            }
        }
    }

    private function getLessonTitle($index, $type)
    {
        $prefixes = [
            'video' => ['Introduction to', 'Understanding', 'Exploring', 'Mastering', 'Deep Dive into'],
            'article' => ['Guide to', 'Overview of', 'Explanation of', 'Study on', 'Analysis of'],
            'quiz' => ['Knowledge Check:', 'Quick Test:', 'Assessment:', 'Review Quiz:', 'Checkpoint:'],
        ];

        $topics = [
            'Basic Concepts',
            'Core Principles',
            'Advanced Techniques',
            'Practical Examples',
            'Real-world Applications',
            'Common Patterns',
            'Best Practices',
            'Troubleshooting',
            'Performance Optimization',
            'Security Considerations',
        ];

        $prefix = $prefixes[$type][array_rand($prefixes[$type])];
        $topic = $topics[array_rand($topics)];

        return "$prefix $topic";
    }

    private function getContentUrl($type)
    {
        return $type === 'video'
            ? 'https://example.com/videos/'.Str::random(10)
            : ($type === 'article'
                ? 'https://example.com/articles/'.Str::random(10)
                : 'https://example.com/quizzes/'.Str::random(10));
    }
}
