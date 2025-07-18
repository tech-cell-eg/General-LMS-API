<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    // make it each filed under each the previous one not in the same line 
    protected $fillable = [
        'user_id',
        'course_id',
        'order_id',
        'progress_percentage',
        'completed_at'
    ];

    /**
     * Get the user for the enrollment.
     * return the user that the enrollment belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course for the enrollment.
     * return the course that the enrollment belongs to
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the order for the enrollment.
     * return the order that the enrollment belongs to
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
