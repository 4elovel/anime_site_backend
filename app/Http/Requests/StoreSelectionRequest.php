<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreSelectionRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:128'],
            'slug' => ['nullable', 'string', 'max:128', Rule::unique('selections', 'slug')],
            'description' => ['required', 'string', 'max:512'],
            'user_id' => ['required', 'string', 'exists:users,id'],
            'is_published' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'poster' => ['nullable', 'image', 'max:5120'],
            'meta_title' => ['nullable', 'string', 'max:128'],
            'meta_description' => ['nullable', 'string', 'max:376'],
            'meta_image' => ['nullable', 'image', 'max:2048'],
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
            'user_id' => 'користувач',
            'is_published' => 'опубліковано',
            'is_active' => 'активно',
            'poster' => 'постер',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_image' => 'meta зображення',
        ];
    }

    /**
     * Підготовка даних перед валідацією.
     */
    protected function prepareForValidation(): void
    {
        // Якщо user_id не вказаний, використовуємо ID поточного користувача
        if (!$this->has('user_id')) {
            $this->merge([
                'user_id' => Auth::id(),
            ]);
        }
    }
}
