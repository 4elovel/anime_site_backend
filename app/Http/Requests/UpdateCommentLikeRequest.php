<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentLikeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment_id' => [
                'sometimes', // Дозволяємо не передавати, якщо не змінюється
                'ulid',
                'exists:comments,id',
            ],
            'is_liked' => [
                'required', // Для оновлення це поле обов’язкове
                'boolean',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'comment_id.ulid' => 'ID коментаря має бути у форматі ULID.',
            'comment_id.exists' => 'Вказаний коментар не існує.',
            'is_liked.required' => 'Необхідно вказати, чи це лайк, чи дизлайк.',
            'is_liked.boolean' => 'Значення має бути true (лайк) або false (дизлайк).',
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
