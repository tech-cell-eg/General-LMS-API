<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Testimonial extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'is_featured'
    ];

    /**
     * Get the user for the testimonial.
     * return the user that the testimonial belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
