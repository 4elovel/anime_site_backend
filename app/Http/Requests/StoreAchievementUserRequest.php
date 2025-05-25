<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAchievementUserRequest extends FormRequest
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
            'user_id' => [
                'required',
                'ulid',
                'exists:users,id', // Перевірка, що користувач існує
            ],
            'achievement_id' => [
                'required',
                'ulid',
                'exists:achievements,id', // Перевірка, що досягнення існує
                Rule::unique('achievement_user')->where(function ($query) {
                    return $query->where('user_id', $this->input('user_id'));
                }),
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
            'user_id.required' => 'ID користувача є обов\'язковим.',
            'user_id.ulid' => 'ID користувача має бути у форматі ULID.',
            'user_id.exists' => 'Вказаний користувач не існує.',
            'achievement_id.required' => 'ID досягнення є обов\'язковим.',
            'achievement_id.ulid' => 'ID досягнення має бути у форматі ULID.',
            'achievement_id.exists' => 'Вказане досягнення не існує.',
            'achievement_id.unique' => 'Це досягнення вже призначено цьому користувачу.',
            'progress_count.required' => 'Прогрес є обов\'язковим.',
            'progress_count.integer' => 'Прогрес має бути цілим числом.',
            'progress_count.min' => 'Прогрес не може бути від\'ємним.',
        ];
    }
}
