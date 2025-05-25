<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Авторизація не потрібна для відновлення пароля
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:128',
                'exists:users,email',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Електронна адреса є обов\'язковою.',
            'email.email' => 'Введіть коректну електронну адресу.',
            'email.max' => 'Електронна адреса не може перевищувати 128 символів.',
            'email.exists' => 'Користувача з такою електронною адресою не знайдено.',
        ];
    }
}
