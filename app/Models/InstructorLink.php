<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorLink extends Model
{
    protected $fillable = ['instructor_id', 'title', 'url', 'icon_class'];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
