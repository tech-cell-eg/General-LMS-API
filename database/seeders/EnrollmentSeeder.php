<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run()
    {
        $orders = Order::with('items')->get();

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                Enrollment::create([
                    'user_id' => $order->user_id,
                    'course_id' => $item->course_id,
                    'order_id' => $order->id,
                    'progress_percentage' => rand(0, 100),
                    'completed_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                ]);
            }
        }

        // Also enroll some students in free courses (without orders)
        $freeCourses = Course::where('price', 0)->get();
        $students = User::where('role', 'student')->get();

        if ($freeCourses->count() > 0) {
            foreach ($students as $student) {
                $freeCourse = $freeCourses->random();

                Enrollment::create([
                    'user_id' => $student->id,
                    'course_id' => $freeCourse->id,
                    'progress_percentage' => rand(0, 100),
                ]);
            }
        }
    }
}