<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRatingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'ulid',
                'exists:ratings,id', // Перевірка, що рейтинг існує
            ],
            'anime_id' => [
                'required',
                'ulid',
                'exists:animes,id',
                Rule::unique('ratings')->where('user_id', auth()->id()),
            ],
            'number' => [
                'required',
                'integer',
                'min:1',
                'max:10',
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
            'id.required' => 'ID рейтингу є обов’язковим.',
            'id.ulid' => 'ID рейтингу має бути у форматі ULID.',
            'id.exists' => 'Вказаний рейтинг не існує.',
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
