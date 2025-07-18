<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseMetadata;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $instructors = User::where('role', 'instructor')->get();
        $categories = Category::all();

        $courses = [
            [
                'title' => 'Complete Web Development Bootcamp',
                'category' => 'Web Development', // Added category
                'short_description' => 'Learn web development from scratch',
                'price' => 199.99,
                'difficulty_level' => 'beginner',
                'languages' => ['English', 'Spanish'],
                'learning_outcomes' => ['Build full-stack applications', 'Understand REST APIs', 'Deploy to production'],
            ],
            [
                'title' => 'Advanced Python Programming',
                'category' => 'Programming', // Added category
                'short_description' => 'Master Python for professional development',
                'price' => 149.99,
                'difficulty_level' => 'intermediate',
                'languages' => ['English'],
                'learning_outcomes' => ['Advanced Python patterns', 'Concurrency', 'Performance optimization'],
            ],
            [
                'title' => 'Machine Learning Fundamentals',
                'category' => 'Data Science', // Added category
                'short_description' => 'Introduction to ML concepts and algorithms',
                'price' => 249.99,
                'difficulty_level' => 'intermediate',
                'languages' => ['English', 'French'],
                'learning_outcomes' => ['Understand ML algorithms', 'Build predictive models', 'Evaluate model performance'],
            ],
            [
                'title' => 'Mobile App Development with Flutter',
                'category' => 'Mobile Development', // Added category
                'short_description' => 'Build cross-platform mobile apps',
                'price' => 179.99,
                'difficulty_level' => 'beginner',
                'languages' => ['English', 'German'],
                'learning_outcomes' => ['Build iOS/Android apps', 'State management', 'Firebase integration'],
            ],
            [
                'title' => 'DevOps and Cloud Deployment',
                'category' => 'DevOps', // Added category
                'short_description' => 'Modern deployment strategies',
                'price' => 299.99,
                'difficulty_level' => 'advanced',
                'languages' => ['English'],
                'learning_outcomes' => ['CI/CD pipelines', 'Containerization', 'Cloud infrastructure'],
            ],
        ];

        foreach ($courses as $index => $courseData) {
            $instructor = $instructors[$index % $instructors->count()];

            // Safely get category with null check
            $category = isset($courseData['category'])
                ? $categories->firstWhere('name', $courseData['category'])
                : null;

            $course = Course::create([
                'instructor_id' => $instructor->id,
                'category_id' => $category ? $category->id : null,
                'title' => $courseData['title'],
                'slug' => Str::slug($courseData['title']),
                'short_description' => $courseData['short_description'],
                'full_description' => $this->getFullDescription($courseData['title']),
                'thumbnail_url' => $this->getRandomThumbnail(),
                'price' => $courseData['price'],
                'discount_price' => rand(0, 1) ? $courseData['price'] * 0.8 : null,
                'is_featured' => $index < 2,
                'certification_available' => rand(0, 1),
                'difficulty_level' => $courseData['difficulty_level'],
                'status' => 'published',
                'total_hours' => rand(10, 50),
                'total_lectures' => rand(15, 100),
                'published_at' => now()->subDays(rand(1, 30)),
            ]);

            CourseMetadata::create([
                'course_id' => $course->id,
                'languages' => $courseData['languages'],
                'prerequisites' => $this->getPrerequisites($courseData['difficulty_level']),
                'learning_outcomes' => $courseData['learning_outcomes'],
                'target_audience' => $this->getTargetAudience($courseData['difficulty_level']),
            ]);
        }
    }

    private function getFullDescription($title)
    {
        return "This comprehensive course on $title will take you from beginner to advanced level. You'll learn all the fundamental concepts and practical skills needed to become proficient. Through hands-on projects and real-world examples, you'll gain the confidence to apply these skills in professional settings.";
    }

    private function getRandomThumbnail()
    {
        $thumbnails = [
            'https://source.unsplash.com/random/800x600/?web-development',
            'https://source.unsplash.com/random/800x600/?programming',
            'https://source.unsplash.com/random/800x600/?coding',
            'https://source.unsplash.com/random/800x600/?technology',
            'https://source.unsplash.com/random/800x600/?computer',
        ];

        return $thumbnails[array_rand($thumbnails)];
    }

    private function getPrerequisites($level)
    {
        return $level === 'beginner'
            ? 'No prior experience required'
            : 'Basic programming knowledge recommended';
    }

    private function getTargetAudience($level)
    {
        return $level === 'beginner'
            ? 'Beginners who want to start a career in this field'
            : ($level === 'intermediate'
                ? 'Developers looking to advance their skills'
                : 'Experienced professionals seeking mastery');
    }
}
