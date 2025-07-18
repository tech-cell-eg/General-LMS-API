<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id', 'headline', 'bio', 'language_preferences', 'social_links'
    ];

    protected $casts = [
        'language_preferences' => 'array',
        'social_links' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
