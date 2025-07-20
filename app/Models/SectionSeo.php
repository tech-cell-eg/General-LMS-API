<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionSeo extends Model
{
    protected $table = 'section_seo';
    protected $fillable = [
        'section_id',
        'description',
        'ppt_title'
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}
