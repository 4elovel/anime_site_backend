<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSearchHistoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'query' => [
                'required',
                'string',
                'max:248', // Обмеження довжини запиту
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Пошуковий запит є обов’язковим.',
            'query.string' => 'Пошуковий запит має бути рядком.',
            'query.max' => 'Пошуковий запит не може перевищувати 248 символів.',
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
