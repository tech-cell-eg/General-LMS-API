<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'avatar_url',
        'role',
        'email_verified_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the profile for the user.
     * return the profile for the user
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the instructor profile for the user.
     * return the instructor profile for the user
     */
    public function instructorProfile(): HasOne
    {
        return $this->hasOne(Instructor::class);
    }

    /**
     * Get the courses for the user.
     * return the courses for the user
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get the enrollments for the user.
     * return the enrollments for the user
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the reviews for the user.
     * return the reviews for the user
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the cart for the user.
     * return the cart for the user
     */
    public function cart(): HasOne
    {
        return $this->hasOne(ShoppingCart::class);
    }

    /**
     * Get the orders for the user.
     * return the orders for the user
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the testimonials for the user.
     * return the testimonials for the user
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /**
     * Get the sent messages for the user.
     * return the sent messages for the user
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the received messages for the user.
     * return the received messages for the user
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    /**
     * Get the instructor links for the user.
     * return the instructor links for the user
     */
    public function instructorLinks(): HasMany
    {
        return $this->hasMany(InstructorLink::class, 'instructor_id');
    }

    /**
     * Get the image url for the user.
     * return the image url for the user
     */
    public function ImageUrlAttribute()
    {
        return $this->avatar_url ? asset('storage/'.$this->avatar_url) : null;
    }

    /**
     * Get the avatar url for the user.
     * return the avatar url for the user
     */
    public function getAvatarUrlAttribute($value)
    {
        return $value ? asset('storage/'.$value) : null;
    }

    /**
     * Get the is instructor for the user.
     * return the is instructor for the user
     */
    public function isInstructor()
    {
        return $this->type === 'instructor'; // or whatever your instructor check is
    }

    /**
     * Get the total students for the user.
     * return the total students for the user
     */
    public function totalStudents()
    {
        return $this->courses()
            ->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->distinct('enrollments.user_id')
            ->count('enrollments.user_id');
    }

    /**
     * Get the average rating for the user.
     * return the average rating for the user
     */
    public function averageRating()
    {
        return $this->courses()
            ->join('reviews', function ($join) {
                $join->on('courses.id', '=', 'reviewable_id')
                    ->where('reviewable_type', Course::class);
            })
            ->avg('reviews.rating');
    }
}
