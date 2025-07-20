<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Resource extends Model
{
    // make it each filed under each the previous one not in the same line
    protected $fillable = [
        'lesson_id',
        'type',
        'title',
        'url_or_path',
        'description'
    ];

    /**
     * Get the lesson for the resource.
     * return the lesson that the resource belongs to
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    // Accessor for API compatibility
    public function getUploadFileAttribute()
    {
        return $this->url_or_path;
    }

    // Mutator for API compatibility
    public function setUploadFileAttribute($value)
    {
        $this->attributes['url_or_path'] = $value;
    }
}
