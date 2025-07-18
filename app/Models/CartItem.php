<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    // make it each filed under each the previous one not in the same line 
    protected $fillable = [
        'cart_id',
        'course_id',
        'price_at_addition',
        'discount_at_addition'
    ];

    /**
     * Get the cart for the cart item.
     * return the cart that the cart item belongs to
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(ShoppingCart::class, 'cart_id'); // explicitly set the foreign key
    }

    /**
     * Get the course for the cart item.
     * return the course that the cart item belongs to
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
