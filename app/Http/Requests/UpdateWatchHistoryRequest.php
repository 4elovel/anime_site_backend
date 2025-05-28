<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWatchHistoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'ulid',
                'exists:watch_histories,id', // Перевірка, що запис існує
            ],
            'episode_id' => [
                'required',
                'ulid',
                'exists:episodes,id',
            ],
            'progress_time' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'ID історії перегляду є обов’язковим.',
            'id.ulid' => 'ID історії перегляду має бути у форматі ULID.',
            'id.exists' => 'Вказаний запис історії перегляду не існує.',
            'episode_id.required' => 'ID епізоду є обов’язковим.',
            'episode_id.ulid' => 'ID епізоду має бути у форматі ULID.',
            'episode_id.exists' => 'Вказаний епізод не існує.',
            'progress_time.integer' => 'Прогрес часу має бути цілим числом.',
            'progress_time.min' => 'Прогрес часу не може бути від’ємним.',
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
