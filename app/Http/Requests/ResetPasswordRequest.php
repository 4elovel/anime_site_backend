<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Авторизація не потрібна для скидання пароля
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token' => [
                'required',
                'string',
            ],
            'email' => [
                'required',
                'email',
                'max:128',
                'exists:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'token.required' => 'Токен є обов\'язковим.',
            'email.required' => 'Електронна адреса є обов\'язковою.',
            'email.email' => 'Введіть коректну електронну адресу.',
            'email.max' => 'Електронна адреса не може перевищувати 128 символів.',
            'email.exists' => 'Користувача з такою електронною адресою не знайдено.',
            'password.required' => 'Пароль є обов\'язковим.',
            'password.min' => 'Пароль повинен містити щонайменше 8 символів.',
            'password.confirmed' => 'Підтвердження пароля не співпадає.',
        ];
    }
}
