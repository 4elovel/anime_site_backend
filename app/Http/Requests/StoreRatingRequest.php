<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRatingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'anime_id' => [
                'required',
                'ulid',
                'exists:animes,id', // Перевірка, що аніме існує
                Rule::unique('ratings')->where('user_id', auth()->id()),
            ],
            'number' => [
                'required',
                'integer',
                'min:1',
                'max:10', // Обмеження для рейтингу від 1 до 10
            ],
            'review' => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'anime_id.required' => 'ID аніме є обов’язковим.',
            'anime_id.ulid' => 'ID аніме має бути у форматі ULID.',
            'anime_id.exists' => 'Вказане аніме не існує.',
            'number.required' => 'Рейтинг є обов’язковим.',
            'number.integer' => 'Рейтинг має бути цілим числом.',
            'number.min' => 'Рейтинг не може бути меншим за 1.',
            'number.max' => 'Рейтинг не може перевищувати 10.',
            'review.string' => 'Відгук має бути рядком.',
            'anime_id.unique' => 'Ви вже залишили рейтинг для цього аніме.',
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
