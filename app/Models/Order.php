<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'subtotal',
        'discount',
        'tax',
        'total',
        'payment_method',
        'payment_status',
        'billing_address',
    ];

    protected $casts = [
        'billing_address' => 'array',
    ];

    /**
     * Get the user for the order.
     * return the user that the order belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     * return the items for the order
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }   

    /**
     * Get the enrollments for the order.
     * return the enrollments for the order
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
