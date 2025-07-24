<?php

namespace App\Models;

use App\Models\InstructorLink;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Instructor extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'areas_of_expertise',
        'professional_experience',
        'total_students',
        'average_rating',
    ];

    protected $casts = [
        'areas_of_expertise' => 'array',
    ];

    /**
     * Get the user for the instructor.
     * return the user that the instructor belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the courses for the instructor.
     * return the courses for the instructor
     */
    public function courses(): HasManyThrough
    {
        return $this->hasManyThrough(Course::class, User::class, 'id', 'instructor_id');
    }

      public function links(): HasMany
    {
        return $this->hasMany(InstructorLink::class, 'instructor_id');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
