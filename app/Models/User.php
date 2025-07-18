<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function instructorProfile()
    {
        return $this->hasOne(Instructor::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cart()
    {
        return $this->hasOne(ShoppingCart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function instructorLinks()
    {
        return $this->hasMany(InstructorLink::class, 'instructor_id');
    }

    public function ImageUrlAttribute()
    {
        return $this->avatar_url ? asset('storage/'.$this->avatar_url) : null;
    }

    public function getAvatarUrlAttribute($value)
    {
        return $value ? asset('storage/'.$value) : null;
    }

    public function isInstructor()
    {
        return $this->type === 'instructor'; // or whatever your instructor check is
    }

    public function totalStudents()
    {
        return $this->courses()
            ->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->distinct('enrollments.user_id')
            ->count('enrollments.user_id');
    }

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
