<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run()
    {
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            // 30% chance to have a testimonial
            if (rand(1, 100) <= 30) {
                Testimonial::create([
                    'user_id' => $student->id,
                    'content' => $this->getTestimonialContent(),
                    'is_featured' => rand(1, 100) <= 20, // 20% chance to be featured
                ]);
            }
        }
    }

    private function getTestimonialContent()
    {
        $contents = [
            'This platform has transformed my career. The courses are top-notch and the instructors are experts in their fields.',
            "I've taken several courses here and each one has exceeded my expectations. The quality of content is exceptional.",
            "The learning experience is engaging and practical. I've been able to apply what I learned directly to my job.",
            'As a busy professional, I appreciate the flexibility of learning at my own pace without sacrificing content quality.',
            'The community and support around these courses make all the difference. I never feel alone in my learning journey.',
            'Worth every penny! The certifications helped me land a promotion within months of completing my courses.',
            "The instructors don't just teach - they mentor. You can tell they genuinely care about student success.",
        ];

        return $contents[array_rand($contents)];
    }
}
