<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAchievementRequest extends FormRequest
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
            'id' => [
                'required',
                'ulid',
                'exists:achievements,id', // Перевірка, що досягнення існує
            ],
            'slug' => [
                'sometimes',
                'string',
                'max:128',
                Rule::unique('achievements', 'slug')->ignore($this->id),
            ],
            'name' => [
                'sometimes',
                'string',
                'max:248',
            ],
            'description' => [
                'sometimes',
                'string',
                'max:512',
            ],
            'icon' => [
                'sometimes',
                'nullable',
                'string',
                'max:2048',
            ],
            'max_counts' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'meta_title' => [
                'sometimes',
                'nullable',
                'string',
                'max:128',
            ],
            'meta_description' => [
                'sometimes',
                'nullable',
                'string',
                'max:376',
            ],
            'meta_image' => [
                'sometimes',
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
            'id.required' => 'ID досягнення є обов\'язковим.',
            'id.ulid' => 'ID досягнення має бути у форматі ULID.',
            'id.exists' => 'Вказане досягнення не існує.',
            'slug.string' => 'Слаг має бути рядком.',
            'slug.max' => 'Слаг не може перевищувати 128 символів.',
            'slug.unique' => 'Цей слаг вже використовується.',
            'name.string' => 'Назва має бути рядком.',
            'name.max' => 'Назва не може перевищувати 248 символів.',
            'description.string' => 'Опис має бути рядком.',
            'description.max' => 'Опис не може перевищувати 512 символів.',
            'icon.string' => 'Іконка має бути рядком.',
            'icon.max' => 'Шлях до іконки не може перевищувати 2048 символів.',
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
