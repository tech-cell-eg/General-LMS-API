<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            InstructorSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
            SectionSeeder::class,
            LessonSeeder::class,
            ResourceSeeder::class,
            ReviewSeeder::class,
            ShoppingCartSeeder::class,
            OrderSeeder::class,
            EnrollmentSeeder::class,
            TestimonialSeeder::class,
            MessageSeeder::class,
            InstructorLinkSeeder::class,
        ]);
    }
}