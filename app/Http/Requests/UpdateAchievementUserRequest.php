<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAchievementUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Авторизація буде перевірена в екшині через Gate
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'ulid',
                'exists:achievement_user,id', // Перевірка, що запис існує
            ],
            'progress_count' => [
                'required',
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
            'id.required' => 'ID запису є обов\'язковим.',
            'id.ulid' => 'ID запису має бути у форматі ULID.',
            'id.exists' => 'Вказаний запис не існує.',
            'progress_count.required' => 'Прогрес є обов\'язковим.',
            'progress_count.integer' => 'Прогрес має бути цілим числом.',
            'progress_count.min' => 'Прогрес не може бути від\'ємним.',
        ];
    }
}
