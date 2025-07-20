<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'offer_name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:coupons,code,' . $this->coupon,
            'amount' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:draft,active,expired,scheduled',
            'quantity' => 'nullable|integer|min:0',
            'redemptions' => 'sometimes|integer|min:0',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'uses_per_customer' => 'sometimes|integer|min:1',
            'priority' => 'sometimes|integer|min:0',
            'discount_type' => 'sometimes|in:percentage,fixed',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time'
        ];
    }
}
