<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    // make it each filed under each the previous one not in the same line 
    protected $fillable = [
        'user_id',
        'rating',
        'title',
        'comment',
        'reviewable_type',
        'reviewable_id'
    ];

    /**
     * Get the user for the review.
     * return the user that the review belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reviewable for the review.
     * return the reviewable that the review belongs to
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }
}
