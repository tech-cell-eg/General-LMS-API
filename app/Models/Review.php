<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'rating',
        'title',
        'comment',
        'answer',
        'answered_at',
        'reviewable_type',
        'reviewable_id'
    ];

    protected $casts = [
        'answered_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeHasComment($query)
    {
        return $query->whereNotNull('comment');
    }

    public function scopeNotAnswered($query)
    {
        return $query->whereNull('answer');
    }
}
