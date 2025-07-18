<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    // make it each filed under each the previous one not in the same line 
    protected $fillable = [
        'order_id',
        'course_id',
        'price',
        'discount',
        'final_price'
    ];

    /**
     * Get the order for the order item.
     * return the order that the order item belongs to
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the course for the order item.
     * return the course that the order item belongs to
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
