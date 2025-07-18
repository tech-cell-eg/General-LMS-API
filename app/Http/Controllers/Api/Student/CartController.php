<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\ShoppingCart;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    use ApiResponse;

    public function getCart()
    {
        try {
            $cart = ShoppingCart::firstOrCreate(['user_id' => Auth::id()]);
            $cart->load('items.course');

            return $this->success([
                'cart' => $cart,
                'total' => $this->calculateTotal($cart),
            ], 'Cart retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve cart: '.$e->getMessage(), 500);
        }
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);
        $cart = ShoppingCart::firstOrCreate(['user_id' => Auth::id()]);

        // Check if course already in cart
        if ($cart->items()->where('course_id', $course->id)->exists()) {
            return $this->error('Course already in cart', 400);
        }

        CartItem::create([
            'cart_id' => $cart->id,
            'course_id' => $course->id,
            'price_at_addition' => $course->price,
            'discount_at_addition' => $course->discount_price,
        ]);

        return $this->getCart();
    }

    public function removeFromCart($itemId)
    {
        $cart = ShoppingCart::where('user_id', Auth::id())->firstOrFail();
        $cart->items()->where('id', $itemId)->delete();

        return $this->getCart();
    }

    protected function calculateTotal(ShoppingCart $cart)
    {
        return $cart->items->reduce(function ($carry, $item) {
            return $carry + ($item->discount_at_addition ?? $item->price_at_addition);
        }, 0);
    }
}
