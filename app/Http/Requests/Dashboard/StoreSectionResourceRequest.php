<?php

namespace App\Http\Requests\Api\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreSectionResourceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|in:video,article,quiz,download',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'upload_file' => 'required|url',
            'upload_thumbnail' => 'nullable|url'
        ];
    }
}
