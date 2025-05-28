<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSearchHistoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'ulid',
                'exists:search_histories,id', // Перевірка, що запис існує
            ],
            'query' => [
                'required',
                'string',
                'max:248',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'ID історії пошуку є обов’язковим.',
            'id.ulid' => 'ID історії пошуку має бути у форматі ULID.',
            'id.exists' => 'Вказаний запис історії пошуку не існує.',
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
