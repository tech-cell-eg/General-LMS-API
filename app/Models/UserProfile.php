<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'headline',
        'bio',
        'language_preferences',
        'social_links',
    ];

    /**
     * Get the user for the user profile.
     * return the user that the user profile belongs to
     */
    protected $casts = [
        'language_preferences' => 'array',
        'social_links' => 'array',
    ];

    /**
     * Get the user for the user profile.
     * return the user that the user profile belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
