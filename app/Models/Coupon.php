<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Coupon extends Model
{
    protected $fillable = [
        'offer_name',
        'code',
        'amount',
        'status',
        'quantity',
        'redemptions',
        'description',
        'category',
        'uses_per_customer',
        'priority',
        'discount_type',
        'start_date',
        'end_date',
        'start_time',
        'end_time'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Scopes for filtering
    public function scopeFilter(Builder $query, array $filters)
    {
        return $query->when($filters['code'] ?? false, fn($query, $code) =>
            $query->where('code', 'like', "%$code%"))
            ->when($filters['amount'] ?? false, fn($query, $amount) =>
                $query->where('amount', $amount))
            ->when($filters['status'] ?? false, fn($query, $status) =>
                $query->where('status', $status))
            ->when($filters['category'] ?? false, fn($query, $category) =>
                $query->where('category', $category))
            ->when($filters['discount_type'] ?? false, fn($query, $type) =>
                $query->where('discount_type', $type))
            ->when($filters['date_from'] ?? false, fn($query, $date) =>
                $query->where('start_date', '>=', $date))
            ->when($filters['date_to'] ?? false, fn($query, $date) =>
                $query->where('start_date', '<=', $date));
    }
}
