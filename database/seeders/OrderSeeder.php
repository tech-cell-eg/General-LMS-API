<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $students = User::where('role', 'student')->get();
        $courses = Course::all();

        foreach ($students as $student) {
            // Each student has 1-3 orders
            $orderCount = rand(1, 3);

            for ($i = 1; $i <= $orderCount; $i++) {
                $orderDate = Carbon::now()->subDays(rand(1, 60));

                $order = Order::create([
                    'user_id' => $student->id,
                    'order_number' => 'ORD-'.strtoupper(Str::random(8)),
                    'subtotal' => 0,
                    'discount' => 0,
                    'tax' => 0,
                    'total' => 0,
                    'payment_method' => $this->getRandomPaymentMethod(),
                    'payment_status' => 'completed',
                    'billing_address' => $this->getBillingAddress(),
                    'created_at' => $orderDate,
                    'completed_at' => $orderDate,
                ]);

                // Add 1-3 courses to each order
                $orderCourses = $courses->random(rand(1, 3));
                $subtotal = 0;

                foreach ($orderCourses as $course) {
                    $discount = $course->discount_price ? $course->price - $course->discount_price : 0;
                    $finalPrice = $course->discount_price ?? $course->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'course_id' => $course->id,
                        'price' => $course->price,
                        'discount' => $discount,
                        'final_price' => $finalPrice,
                    ]);

                    $subtotal += $course->price;
                }

                // Update order totals
                $discount = $order->items->sum('discount');
                $tax = $subtotal * 0.1; // 10% tax
                $total = $subtotal - $discount + $tax;

                $order->update([
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => $tax,
                    'total' => $total,
                ]);
            }
        }
    }

    private function getRandomPaymentMethod()
    {
        $methods = ['credit_card', 'paypal', 'stripe', 'bank_transfer'];

        return $methods[array_rand($methods)];
    }

    private function getBillingAddress()
    {
        return [
            'street' => rand(100, 999).' Main St',
            'city' => ['New York', 'London', 'Tokyo', 'Sydney', 'Berlin'][array_rand([0, 1, 2, 3, 4])],
            'state' => ['NY', 'CA', 'TX', 'FL', 'IL'][array_rand([0, 1, 2, 3, 4])],
            'zip' => rand(10000, 99999),
            'country' => 'United States',
        ];
    }
}
