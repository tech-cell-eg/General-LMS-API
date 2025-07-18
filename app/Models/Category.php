<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'image', 'slug'];

    protected $appends = ['image_url'];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                    return $this->image;
                }

                return $this->image
                    ? asset('storage/categories/'.$this->image)
                    : asset('images/default-category.png');
            }
        );
    }
}
