<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'section_id',
        'title',
        'duration_minutes',
        'content_type',
        'content_url',
        'preview_available',
        'order'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }
}
