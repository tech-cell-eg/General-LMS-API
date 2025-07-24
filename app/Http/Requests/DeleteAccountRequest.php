<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteAccountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'password' => ['required', 'current_password'],
            'confirmation' => ['required', 'string', Rule::in(['DELETE MY ACCOUNT'])],
        ];
    }

    public function messages()
    {
        return [
            'confirmation.in' => 'Please type "DELETE MY ACCOUNT" to confirm.',
        ];
    }
}