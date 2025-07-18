<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Resource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ResourceSeeder extends Seeder
{
    public function run()
    {
        $lessons = Lesson::all();

        foreach ($lessons as $lesson) {
            // Add 0-3 resources per lesson
            $resourceCount = rand(0, 3);

            for ($i = 1; $i <= $resourceCount; $i++) {
                $types = ['file', 'link', 'attachment'];
                $type = $types[array_rand($types)];

                Resource::create([
                    'lesson_id' => $lesson->id,
                    'type' => $type,
                    'title' => $this->getResourceTitle($type),
                    'url_or_path' => $this->getResourceUrl($type),
                    'description' => $this->getResourceDescription($type),
                ]);
            }
        }
    }

    private function getResourceTitle($type)
    {
        $titles = [
            'file' => ['Downloadable PDF', 'Exercise Files', 'Code Samples'],
            'link' => ['Additional Reading', 'Reference Documentation', 'External Tutorial'],
            'attachment' => ['Slide Deck', 'Cheat Sheet', 'Worksheet'],
        ];

        return $titles[$type][array_rand($titles[$type])];
    }

    private function getResourceUrl($type)
    {
        return $type === 'file'
            ? 'https://example.com/files/'.Str::random(10).'.pdf'
            : ($type === 'link'
                ? 'https://example.com/links/'.Str::random(10)
                : 'https://example.com/attachments/'.Str::random(10));
    }

    private function getResourceDescription($type)
    {
        return $type === 'file'
            ? 'Downloadable resource to accompany this lesson'
            : ($type === 'link'
                ? 'Additional reference material for further learning'
                : 'Supplementary material to enhance your understanding');
    }
}
