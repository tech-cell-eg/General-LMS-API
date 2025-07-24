<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        'published_at',
    ];

    protected $dates = ['published_at'];

    /**
     * Get the instructor for the course.
     * return the instructor that the course belongs to
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the metadata for the course.
     * return the metadata for the course
     */
    public function metadata(): HasOne
    {
        return $this->hasOne(CourseMetadata::class);
    }

    /**
     * Get the sections for the course.
     * return the sections for the course
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Get the reviews for the course.
     * return the reviews for the course
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get the enrollments for the course.
     * return the enrollments for the course
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the order items for the course.
     * return the order items for the course
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the cart items for the course.
     * return the cart items for the course
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the category for the course.
     * return the category for the course
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the image url for the course.
     * return the image url for the course
     */
    public function getImageUrlAttribute()
    {
        return $this->thumbnail_url ? asset('storage/' . $this->thumbnail_url) : null;
    }


   public function scopeSearch(Builder $query, string $searchTerm): Builder
{
    return $query->whereHas('instructor') // Only courses with an instructor
        ->where(function($q) use ($searchTerm) {
            $q->where('title', 'like', "%{$searchTerm}%")
            //   ->orWhere('description', 'like', "%{$searchTerm}%")
              ->orWhereHas('instructor', function($q) use ($searchTerm) {
                  $q->where('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('last_name', 'like', "%{$searchTerm}%");
              })
              ->orWhereHas('category', function($q) use ($searchTerm) {
                  $q->where('name', 'like', "%{$searchTerm}%");
              });
        });
}

}