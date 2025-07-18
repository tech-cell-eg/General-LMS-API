<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseMetadata extends Model
{
    protected $fillable = [
        'course_id', 'languages', 'prerequisites', 'learning_outcomes', 'target_audience'
    ];

    protected $casts = [
        'languages' => 'array',
        'learning_outcomes' => 'array'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
