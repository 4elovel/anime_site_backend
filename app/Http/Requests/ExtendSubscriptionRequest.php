<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExtendSubscriptionRequest extends FormRequest
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
            'days' => [
                'required',
                'integer',
                'min:1',
            ],
            'payment_id' => [
                'nullable',
                'ulid',
                'exists:payments,id',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'days.required' => 'Кількість днів є обов\'язковою.',
            'days.integer' => 'Кількість днів має бути цілим числом.',
            'days.min' => 'Кількість днів має бути не менше 1.',
            'payment_id.ulid' => 'ID платежу має бути у форматі ULID.',
            'payment_id.exists' => 'Платіж з таким ID не знайдено.',
        ];
    }
}
