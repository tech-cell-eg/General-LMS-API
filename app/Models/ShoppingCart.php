<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingCart extends Model
{
    protected $fillable = [
        'user_id'
    ];

    /**
     * Get the user for the shopping cart.
     * return the user that the shopping cart belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the shopping cart.
     * return the items for the shopping cart
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id'); // explicitly set the foreign key
    }

    /**
     * Get the cart items for the shopping cart.
     * return the cart items for the shopping cart
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
