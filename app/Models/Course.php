<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'slug',
        'short_description',
        'full_description',
        'thumbnail_url',
        'preview_video_url',
        'price',
        'discount_price',
        'is_featured',
        'certification_available',
        'difficulty_level',
        'status',
        'total_hours',
        'total_lectures',
        'published_at'
    ];

    protected $dates = ['published_at'];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function metadata()
    {
        return $this->hasOne(CourseMetadata::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        return $this->thumbnail_url ? asset('storage/' . $this->thumbnail_url) : null;
    }
}
