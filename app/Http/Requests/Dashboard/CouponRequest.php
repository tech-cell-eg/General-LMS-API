<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'offer_name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:coupons',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,expired,scheduled',
            'quantity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'uses_per_customer' => 'required|integer|min:1',
            'priority' => 'required|integer|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time'
        ];
    }
}
