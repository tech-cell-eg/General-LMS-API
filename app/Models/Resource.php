<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = ['lesson_id', 'type', 'title', 'url_or_path', 'description'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
