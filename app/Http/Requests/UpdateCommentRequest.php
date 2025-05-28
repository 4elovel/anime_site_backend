<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'ulid', // Перевірка, що це дійсний ULID
                'exists:comments,id', // Перевірка, що коментар існує
            ],
            'body' => [
                'required',
                'string',
                'max:5000', // Обмеження довжини тексту коментаря
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
            'id.required' => 'ID коментаря є обов’язковим.',
            'id.ulid' => 'ID коментаря має бути у форматі ULID.',
            'id.exists' => 'Вказаний коментар не існує.',
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
