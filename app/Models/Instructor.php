<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'areas_of_expertise',
        'professional_experience',
        'total_students',
        'average_rating'
    ];

    protected $casts = [
        'areas_of_expertise' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courses()
    {
        return $this->hasManyThrough(Course::class, User::class, 'id', 'instructor_id');
    }
}
