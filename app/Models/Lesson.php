<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    protected $fillable = [
        'section_id',
        'title',
        'duration_minutes',
        'content_type',
        'content_url',
        'preview_available',
        'order',
    ];

     protected $casts = [
        'preview_available' => 'boolean',
        'duration_minutes' => 'integer',
        'order' => 'integer'
    ];
    /**
     * Get the section for the lesson.
     * return the section that the lesson belongs to
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the resources for the lesson.
     * return the resources for the lesson
     */
    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }
}
