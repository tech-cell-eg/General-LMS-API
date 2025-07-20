<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\AddToCartRequest;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\ShoppingCart;
use App\Services\CartService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    use ApiResponse;

    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = ShoppingCart::firstOrCreate(['user_id' => Auth::id()]);
        $cart->load('items.course');

        return $this->success([
            'cart' => $cart,
            'total' => $this->cartService->calculateTotal($cart),
        ], 'Cart retrieved successfully');
    }

    public function store(AddToCartRequest $request)
    {
        $course = Course::findOrFail($request->course_id);
        $cart = ShoppingCart::firstOrCreate(['user_id' => Auth::id()]);

        if ($cart->items()->where('course_id', $course->id)->exists()) {
            return $this->error('Course already in cart', 400);
        }

        CartItem::create([
            'cart_id' => $cart->id,
            'course_id' => $course->id,
            'price_at_addition' => $course->price,
            'discount_at_addition' => $course->discount_price,
        ]);

        return $this->index();
    }

    public function destroy($itemId)
    {
        $cart = ShoppingCart::where('user_id', Auth::id())->firstOrFail();
        $cart->items()->where('id', $itemId)->delete();

        return $this->index();
    }
}
