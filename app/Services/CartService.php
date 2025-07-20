<?php

namespace App\Services;

use App\Models\ShoppingCart;

class CartService
{
    public function calculateTotal(ShoppingCart $cart)
    {
        return $cart->items->reduce(function ($carry, $item) {
            return $carry + ($item->discount_at_addition ?? $item->price_at_addition);
        }, 0);
    }
}
