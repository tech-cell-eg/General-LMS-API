<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'course_id', 'price_at_addition', 'discount_at_addition'];

    public function cart()
    {
        return $this->belongsTo(ShoppingCart::class, 'cart_id'); // explicitly set the foreign key
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }


}
