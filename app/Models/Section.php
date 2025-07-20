<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Section extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'order',
        'description',
        'status'

    ];

     protected $casts = [
        'order' => 'integer'
    ];
    /**
     * Get the course for the section.
     * return the course that the section belongs to
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lessons for the section.
     * return the lessons for the section
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function seo()
    {
        return $this->hasOne(SectionSeo::class);
    }

    // Accessor for section price (gets from course)
    public function getPriceAttribute()
    {
        return $this->course->price;
    }
}
