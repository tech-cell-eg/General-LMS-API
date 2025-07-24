<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . auth()->id(),
            'email' => 'sometimes|email|max:255|unique:users,email,' . auth()->id(),
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string',
            'headline' => 'nullable|string|max:255',
            'language_preferences' => 'nullable|string',
            'social_links' => 'nullable|array',
            'instructor.title' => 'sometimes|string|max:255',
            'instructor.professional_experience' => 'nullable|string',
            'instructor.areas_of_expertise' => 'nullable|array',
            'instructor.areas_of_expertise.*' => 'string|max:255',
            'instructor.links' => 'nullable|array',
            'instructor.links.*.title' => 'required_with:instructor.links|string|max:255',
            'instructor.links.*.url' => 'required_with:instructor.links|url',
            'instructor.links.*.icon_class' => 'nullable|string|max:255'
        ];
    }
}
