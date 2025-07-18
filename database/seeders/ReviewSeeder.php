<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;


class ReviewSeeder extends Seeder
{
    public function run()
    {
        $courses = Course::all();
        $students = User::where('role', 'student')->get();

        foreach ($courses as $course) {
            $reviewCount = rand(5, 15);
            $reviewers = $students->random($reviewCount);

            foreach ($reviewers as $reviewer) {
                Review::create([
                    'user_id' => $reviewer->id,
                    'reviewable_type' => Course::class,
                    'reviewable_id' => $course->id,
                    'rating' => rand(3, 5),
                    'title' => $this->getReviewTitle(),
                    'comment' => $this->getReviewComment($course->title),
                ]);
            }

            // Also create some instructor reviews
            $instructorReviewers = $students->random(rand(3, 8));

            foreach ($instructorReviewers as $reviewer) {
                Review::create([
                    'user_id' => $reviewer->id,
                    'reviewable_type' => User::class,
                    'reviewable_id' => $course->instructor_id,
                    'rating' => rand(4, 5),
                    'title' => $this->getInstructorReviewTitle(),
                    'comment' => $this->getInstructorReviewComment(),
                ]);
            }
        }
    }

    private function getReviewTitle()
    {
        $titles = [
            'Great Course!',
            'Very Helpful',
            'Learned a Lot',
            'Excellent Content',
            'Highly Recommended',
            'Worth Every Penny',
            'Transformative Experience',
            'Perfect for Beginners',
            'Advanced and Insightful',
        ];
        return $titles[array_rand($titles)];
    }

    private function getReviewComment($courseTitle)
    {
        $comments = [
            "This $courseTitle course exceeded my expectations. The instructor explains concepts clearly and provides practical examples.",
            "I've taken several courses on this topic, but this one stands out for its depth and clarity.",
            "The course material is well-organized and the pacing is perfect. I never felt overwhelmed or bored.",
            "The hands-on projects were particularly valuable. I now feel confident applying these skills in real-world scenarios.",
            "The instructor's teaching style is engaging and makes complex topics easy to understand.",
            "I appreciated the mix of theory and practical applications. The quizzes helped reinforce my learning.",
            "The course content is up-to-date with current industry standards. I learned exactly what I needed to know.",
        ];
        return $comments[array_rand($comments)];
    }

    private function getInstructorReviewTitle()
    {
        $titles = [
            'Excellent Instructor',
            'Knowledgeable Teacher',
            'Great Teaching Style',
            'Very Responsive',
            'Supportive Mentor',
        ];
        return $titles[array_rand($titles)];
    }

    private function getInstructorReviewComment()
    {
        $comments = [
            "The instructor has a deep understanding of the subject matter and explains concepts clearly.",
            "Questions were answered promptly and with detailed explanations.",
            "The instructor's enthusiasm for the subject is contagious and made learning enjoyable.",
            "I appreciated the instructor's real-world examples that connected theory to practice.",
            "The feedback on assignments was constructive and helped me improve.",
        ];
        return $comments[array_rand($comments)];
    }
}
