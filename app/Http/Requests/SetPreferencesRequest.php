<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetPreferencesRequest extends FormRequest
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
    public function rules()
    {
        return [
            'sources' => 'nullable|array',
            'sources.*' => 'string|max:255', // Validate each source as a string
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:255', // Validate each category as a string
            'authors' => 'nullable|array',
            'authors.*' => 'string|max:255', // Validate each author as a string
        ];
    }

    public function messages()
    {
        return [
            'sources.array' => 'Sources must be an array.',
            'categories.array' => 'Categories must be an array.',
            'authors.array' => 'Authors must be an array.',
        ];
    }
}
