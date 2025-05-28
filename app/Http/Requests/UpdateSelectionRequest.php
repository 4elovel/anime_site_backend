<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateSelectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:128'],
            'slug' => [
                'sometimes',
                'string',
                'max:128',
                Rule::unique('selections', 'slug')->ignore($this->route('selection'))
            ],
            'description' => ['sometimes', 'string', 'max:512'],
            'is_published' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'poster' => ['sometimes', 'nullable', 'image', 'max:5120'],
            'meta_title' => ['sometimes', 'nullable', 'string', 'max:128'],
            'meta_description' => ['sometimes', 'nullable', 'string', 'max:376'],
            'meta_image' => ['sometimes', 'nullable', 'image', 'max:2048'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'назва',
            'slug' => 'slug',
            'description' => 'опис',
            'is_published' => 'опубліковано',
            'is_active' => 'активно',
            'poster' => 'постер',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_image' => 'meta зображення',
        ];
    }
}
