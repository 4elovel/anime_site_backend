<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'commentable_id' => [
                'required',
                'ulid', // Перевірка, що це дійсний ULID
            ],
            'commentable_type' => [
                'required',
                'string', // Тип моделі (наприклад, AnimeSite\Models\Post)
            ],
            'body' => [
                'required',
                'string',
                'max:5000', // Обмеження довжини тексту коментаря (наприклад, 5000 символів)
            ],
            'is_spoiler' => [
                'nullable',
                'boolean', // Перевірка, що це булеве значення (true/false)
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'commentable_id.required' => 'ID об’єкта коментаря є обов’язковим.',
            'commentable_id.ulid' => 'ID об’єкта коментаря має бути у форматі ULID.',
            'commentable_type.required' => 'Тип об’єкта коментаря є обов’язковим.',
            'commentable_type.string' => 'Тип об’єкта коментаря має бути рядком.',
            'body.required' => 'Текст коментаря є обов’язковим.',
            'body.string' => 'Текст коментаря має бути рядком.',
            'body.max' => 'Текст коментаря не може перевищувати 5000 символів.',
            'is_spoiler.boolean' => 'Поле "спойлер" має бути true або false.',
        ];
    }

    /**
     * Підготовка даних перед валідацією.
     */
    protected function prepareForValidation(): void
    {
        // Додаємо user_id автоматично з авторизованого користувача
        $this->merge([
            'user_id' => auth()->id(),
        ]);
    }
}
