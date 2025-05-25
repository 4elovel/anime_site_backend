<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentCallbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Авторизація не потрібна для колбеку від платіжної системи
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data' => [
                'required',
                'string',
            ],
            'signature' => [
                'required',
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
            'data.required' => 'Дані платежу є обов\'язковими.',
            'data.string' => 'Дані платежу мають бути рядком.',
            'signature.required' => 'Підпис є обов\'язковим.',
            'signature.string' => 'Підпис має бути рядком.',
        ];
    }
}
