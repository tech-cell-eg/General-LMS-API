<?php

namespace App\Http\Requests\Api\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResourceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'sometimes|in:video,article,quiz,download',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'upload_file' => 'sometimes|url',
            'upload_thumbnail' => 'nullable|url'
        ];
    }
}
