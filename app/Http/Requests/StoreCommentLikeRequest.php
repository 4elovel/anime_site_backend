<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentLikeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment_id' => [
                'required',
                'ulid', // Перевірка, що це дійсний ULID
                'exists:comments,id', // Перевірка, що коментар існує
            ],
            'is_liked' => [
                'required',
                'boolean', // Перевірка, що це булеве значення (true/false)
            ],
            // 'user_id' не додаємо в правила, якщо ми беремо його з auth()->id()
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'comment_id.required' => 'ID коментаря є обов’язковим.',
            'comment_id.ulid' => 'ID коментаря має бути у форматі ULID.',
            'comment_id.exists' => 'Вказаний коментар не існує.',
            'is_liked.required' => 'Необхідно вказати, чи це лайк, чи дизлайк.',
            'is_liked.boolean' => 'Значення має бути true (лайк) або false (дизлайк).',
        ];
    }

    /**
     * Підготовка даних перед валідацією (опціонально).
     */
    protected function prepareForValidation(): void
    {
        // Додаємо user_id автоматично з авторизованого користувача
        $this->merge([
            'user_id' => auth()->id(),
        ]);
    }
}
