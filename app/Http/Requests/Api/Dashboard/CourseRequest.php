<?php

namespace App\Http\Requests\Api\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'full_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'intro_video' => 'nullable|file|max:102400',
            'intro_image' => 'nullable|image|mimes:jpeg,png|max:2048',
            'thumbnail' => 'nullable|image|mimes:jpeg,png|max:2048',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced',
            'certification_available' => 'boolean',
            'is_featured' => 'boolean',
            'total_hours' => 'nullable|integer',
            'total_lectures' => 'nullable|integer',
            'languages' => 'nullable|array',
            'prerequisites' => 'nullable|string',
            'learning_outcomes' => 'nullable|array',
            'target_audience' => 'nullable|string',
            'syllabus' => 'nullable|string',
            'faqs' => 'nullable|string',
            'status' => 'nullable|in:draft,published',
        ];
    }
}
