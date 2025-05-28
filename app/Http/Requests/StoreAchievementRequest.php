<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAchievementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Авторизація буде перевірена в екшині через Gate
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slug' => [
                'required',
                'string',
                'max:128',
                'unique:achievements,slug', // Унікальність slug
            ],
            'name' => [
                'required',
                'string',
                'max:248',
            ],
            'description' => [
                'required',
                'string',
                'max:512',
            ],
            'icon' => [
                'nullable',
                'string',
                'max:2048',
            ],
            'max_counts' => [
                'required',
                'integer',
                'min:1',
            ],
            'meta_title' => [
                'nullable',
                'string',
                'max:128',
            ],
            'meta_description' => [
                'nullable',
                'string',
                'max:376',
            ],
            'meta_image' => [
                'nullable',
                'string',
                'max:2048',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'slug.required' => 'Слаг є обов\'язковим.',
            'slug.string' => 'Слаг має бути рядком.',
            'slug.max' => 'Слаг не може перевищувати 128 символів.',
            'slug.unique' => 'Цей слаг вже використовується.',
            'name.required' => 'Назва є обов\'язковою.',
            'name.string' => 'Назва має бути рядком.',
            'name.max' => 'Назва не може перевищувати 248 символів.',
            'description.required' => 'Опис є обов\'язковим.',
            'description.string' => 'Опис має бути рядком.',
            'description.max' => 'Опис не може перевищувати 512 символів.',
            'icon.string' => 'Іконка має бути рядком.',
            'icon.max' => 'Шлях до іконки не може перевищувати 2048 символів.',
            'max_counts.required' => 'Максимальна кількість є обов\'язковою.',
            'max_counts.integer' => 'Максимальна кількість має бути цілим числом.',
            'max_counts.min' => 'Максимальна кількість має бути не менше 1.',
            'meta_title.string' => 'Мета-заголовок має бути рядком.',
            'meta_title.max' => 'Мета-заголовок не може перевищувати 128 символів.',
            'meta_description.string' => 'Мета-опис має бути рядком.',
            'meta_description.max' => 'Мета-опис не може перевищувати 376 символів.',
            'meta_image.string' => 'Мета-зображення має бути рядком.',
            'meta_image.max' => 'Шлях до мета-зображення не може перевищувати 2048 символів.',
        ];
    }
}
