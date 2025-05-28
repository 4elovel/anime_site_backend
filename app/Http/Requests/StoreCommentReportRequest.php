<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\CommentReportType;

class StoreCommentReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment_id' => [
                'required',
                'ulid',
                'exists:comments,id', // Перевірка існування коментаря
            ],
            'type' => [
                'required',
                Rule::enum(CommentReportType::class), // Перевірка, що значення належить енуму
            ],
            'body' => [
                'nullable',
                'string',
                'max:1000', // Обмеження довжини тексту, наприклад, 1000 символів
            ],
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
            'type.required' => 'Тип репорту є обов’язковим.',
            'type.enum' => 'Невірний тип репорту.',
            'body.string' => 'Опис має бути текстовим.',
            'body.max' => 'Опис не може перевищувати 1000 символів.',
        ];
    }

    /**
     * Підготовка даних перед валідацією.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => auth()->id(), // Автоматично додаємо ID користувача
            'is_viewed' => false, // Значення за замовчуванням
        ]);
    }
}
