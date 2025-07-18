<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorLink extends Model
{
    // make it each filed under each the previous one not in the same line 
    protected $fillable = [
        'instructor_id',
        'title',
        'url',
        'icon_class'
    ];

    /**
     * Get the instructor for the instructor link.
     * return the instructor that the instructor link belongs to
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
