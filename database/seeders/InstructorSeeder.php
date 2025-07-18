<?php

namespace Database\Seeders;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Database\Seeder;

class InstructorSeeder extends Seeder
{
    public function run()
    {
        $instructors = User::where('role', 'instructor')->get();

        $expertiseAreas = [
            ['Web Development', 'JavaScript', 'React'],
            ['Data Science', 'Python', 'Machine Learning'],
            ['Mobile Development', 'Flutter', 'Dart'],
            ['DevOps', 'AWS', 'Docker'],
            ['UI/UX Design', 'Figma', 'Adobe XD'],
        ];

        $instructors->each(function ($user, $index) use ($expertiseAreas) {
            Instructor::create([
                'user_id' => $user->id,
                'title' => $this->getRandomTitle(),
                'areas_of_expertise' => $expertiseAreas[$index] ?? ['General Education'],
                'professional_experience' => $this->getProfessionalExperience(),
            ]);
        });
    }

    private function getRandomTitle()
    {
        $titles = [
            'Senior Instructor',
            'Professional Educator',
            'Industry Expert',
            'Lead Trainer',
            'Certified Instructor',
        ];

        return $titles[array_rand($titles)];
    }

    private function getProfessionalExperience()
    {
        $experiences = [
            '10+ years of experience in software development and education',
            'Former lead developer at a Fortune 500 company',
            'Published author and conference speaker',
            'Created multiple popular online courses with thousands of students',
            'Passionate educator with a focus on practical skills',
        ];

        return $experiences[array_rand($experiences)];
    }
}
