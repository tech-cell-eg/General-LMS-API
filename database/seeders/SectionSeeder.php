<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run()
    {
        $courses = Course::all();

        foreach ($courses as $course) {
            $sectionCount = rand(4, 8);

            for ($i = 1; $i <= $sectionCount; $i++) {
                Section::create([
                    'course_id' => $course->id,
                    'title' => $this->getSectionTitle($i),
                    'order' => $i,
                    'description' => $this->getSectionDescription($i),
                ]);
            }
        }
    }

    private function getSectionTitle($index)
    {
        $titles = [
            'Introduction',
            'Fundamentals',
            'Core Concepts',
            'Advanced Topics',
            'Practical Applications',
            'Project Work',
            'Best Practices',
            'Next Steps',
        ];
        return $titles[$index - 1] ?? "Module $index";
    }

    private function getSectionDescription($index)
    {
        $descriptions = [
            'Get started with the basics and course overview',
            'Learn the foundational concepts you need to proceed',
            'Dive deeper into the core principles',
            'Explore more advanced techniques and patterns',
            'Apply what you\'ve learned to real-world scenarios',
            'Work on a comprehensive project to solidify your skills',
            'Learn industry standards and professional approaches',
            'Where to go from here and additional resources',
        ];
        return $descriptions[$index - 1] ?? "This section covers important concepts for this course";
    }
}