<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShoppingCart;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    use ApiResponse;

    public function checkout(Request $request)
    {
        $cart = ShoppingCart::with('items.course')
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return $this->error('Cart is empty', 400);
        }

        return DB::transaction(function () use ($cart, $request) {
            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-'.strtoupper(uniqid()),
                'subtotal' => $this->calculateSubtotal($cart),
                'discount' => $this->calculateDiscount($cart),
                'tax' => $this->calculateTax($cart),
                'total' => $this->calculateTotal($cart),
                'payment_method' => $request->payment_method ?? 'stripe',
                'payment_status' => 'pending',
                'billing_address' => $request->billing_address,
            ]);

            // Create order items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $item->course_id,
                    'price' => $item->price_at_addition,
                    'discount' => $item->discount_at_addition,
                    'final_price' => $item->discount_at_addition ?? $item->price_at_addition,
                ]);
            }

            // Clear cart
            $cart->items()->delete();

            // Process payment (would integrate with Stripe/PayPal here)
            // $this->processPayment($order);

            return $this->success([
                'order' => $order,
                'items' => $order->items,
            ], 'Checkout successful');
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

    protected function calculateTotal(ShoppingCart $cart)
    {
        return $this->calculateSubtotal($cart) - $this->calculateDiscount($cart) + $this->calculateTax($cart);
    }
}
