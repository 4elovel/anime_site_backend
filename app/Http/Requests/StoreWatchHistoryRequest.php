<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWatchHistoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'episode_id' => [
                'required',
                'ulid',
                'exists:episodes,id', // Перевірка, що епізод існує
                Rule::unique('watch_histories')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }), // Унікальність для user_id та episode_id
            ],
            'progress_time' => [
                'nullable',
                'integer',
                'min:0', // Прогрес часу не може бути від’ємним
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'episode_id.required' => 'ID епізоду є обов’язковим.',
            'episode_id.ulid' => 'ID епізоду має бути у форматі ULID.',
            'episode_id.exists' => 'Вказаний епізод не існує.',
            'episode_id.unique' => 'Ви вже маєте запис історії перегляду для цього епізоду.',
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
