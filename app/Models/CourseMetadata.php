<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMetadata extends Model
{
    // make it each filed under each the previous one not in the same line 
    protected $fillable = [
        'course_id',
        'languages',
        'prerequisites',
        'learning_outcomes',
        'target_audience',
    ];

    protected $casts = [
        'languages' => 'array',
        'learning_outcomes' => 'array',
    ];

    /**
     * Get the course for the course metadata.
     * return the course that the course metadata belongs to
     */
    public function course() : BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
