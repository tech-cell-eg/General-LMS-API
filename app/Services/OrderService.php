<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Order;
use App\Models\ShoppingCart;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function processCheckout($userId, array $checkoutData)
    {
        $cart = ShoppingCart::with('items.course')
            ->where('user_id', $userId)
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        return DB::transaction(function () use ($cart, $checkoutData) {
            // Calculate order amounts
            $subtotal = $this->calculateSubtotal($cart);
            $discount = $this->calculateDiscount($cart);
            $tax = $this->calculateTax($cart);
            $total = $this->calculateTotal($subtotal, $discount, $tax);

            // Create order
            $order = Order::create([
                'user_id' => $cart->user_id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'subtotal' => $subtotal,
                'discount' => $discount ?? 0,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $checkoutData['payment_method'] ?? 'stripe',
                'payment_status' => 'pending',
                'billing_address' => $checkoutData['billing_address'],
            ]);

            // Create order items and enrollments
            foreach ($cart->items as $item) {
                $orderItem = $order->items()->create([
                    'course_id' => $item->course_id,
                    'price' => $item->price_at_addition,
                    'discount' => $item->discount_at_addition ?? 0,
                    'final_price' => $item->discount_at_addition ?? $item->price_at_addition,
                ]);

                // Create enrollment for each course
                Enrollment::create([
                    'user_id' => $cart->user_id,
                    'course_id' => $item->course_id,
                    'order_id' => $order->id,
                    'progress_percentage' => 0,
                ]);
            }

            // Clear cart
            $cart->items()->delete();

            return $order;
        });
    }

    protected function calculateSubtotal(ShoppingCart $cart)
    {
        return $cart->items->sum('price_at_addition');
    }

    protected function calculateDiscount(ShoppingCart $cart)
    {
        return $cart->items->reduce(function ($carry, $item) {
            return $carry + ($item->price_at_addition - ($item->discount_at_addition ?? $item->price_at_addition));
        }, 0);
    }

    protected function calculateTax(ShoppingCart $cart)
    {
        return $this->calculateSubtotal($cart) * 0.1; // 10% tax
    }

    protected function calculateTotal($subtotal, $discount, $tax)
    {
        return $subtotal - $discount + $tax;
    }
}
