<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserSubscriptionRequest extends FormRequest
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
                'exists:users,id',
            ],
            'tariff_id' => [
                'required',
                'ulid',
                'exists:tariffs,id',
            ],
            'payment_id' => [
                'nullable',
                'ulid',
                'exists:payments,id',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'auto_renew' => [
                'sometimes',
                'boolean',
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
            'user_id.exists' => 'Користувача з таким ID не знайдено.',
            'tariff_id.required' => 'ID тарифу є обов\'язковим.',
            'tariff_id.ulid' => 'ID тарифу має бути у форматі ULID.',
            'tariff_id.exists' => 'Тариф з таким ID не знайдено.',
            'payment_id.ulid' => 'ID платежу має бути у форматі ULID.',
            'payment_id.exists' => 'Платіж з таким ID не знайдено.',
            'start_date.required' => 'Дата початку є обов\'язковою.',
            'start_date.date' => 'Дата початку має бути валідною датою.',
            'end_date.required' => 'Дата закінчення є обов\'язковою.',
            'end_date.date' => 'Дата закінчення має бути валідною датою.',
            'end_date.after' => 'Дата закінчення має бути пізніше дати початку.',
            'is_active.boolean' => 'Значення активності має бути true або false.',
            'auto_renew.boolean' => 'Значення автоматичного продовження має бути true або false.',
        ];
    }
}
