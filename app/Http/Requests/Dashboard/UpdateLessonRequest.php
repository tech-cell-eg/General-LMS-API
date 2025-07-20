<?php

namespace App\Http\Requests\Api\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|string|max:255',
            'content_type' => 'sometimes|in:video,article,quiz,assignment',
            'content_url' => 'sometimes|url',
            'duration_minutes' => 'sometimes|integer|min:0',
            'preview_available' => 'sometimes|boolean'
        ];
    }
}
