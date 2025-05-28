<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\UserListType;

class StoreUserListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'listable_id' => [
                'required',
                'ulid', // Перевірка, що це дійсний ULID
            ],
            'listable_type' => [
                'required',
                'string', // Тип моделі (наприклад, AnimeSite\Models\Anime)
            ],
            'type' => [
                'required',
                Rule::enum(UserListType::class), // Перевірка на валідне значення енума
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'listable_id.required' => 'ID об’єкта списку є обов’язковим.',
            'listable_id.ulid' => 'ID об’єкта списку має бути у форматі ULID.',
            'listable_type.required' => 'Тип об’єкта списку є обов’язковим.',
            'listable_type.string' => 'Тип об’єкта списку має бути рядком.',
            'type.required' => 'Тип списку є обов’язковим.',
            'type.enum' => 'Невалідне значення для типу списку.',
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
