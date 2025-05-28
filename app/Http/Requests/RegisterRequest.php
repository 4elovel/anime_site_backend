<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\Gender;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Авторизація не потрібна для реєстрації
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:128',
            ],
            'email' => [
                'required',
                'email',
                'max:128',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'gender' => [
                'nullable',
                Rule::enum(Gender::class),
            ],
            'birthday' => [
                'nullable',
                'date',
            ],
            'description' => [
                'nullable',
                'string',
                'max:512',
            ],
            'allow_adult' => [
                'sometimes',
                'boolean',
            ],
            'is_auto_next' => [
                'sometimes',
                'boolean',
            ],
            'is_auto_play' => [
                'sometimes',
                'boolean',
            ],
            'is_auto_skip_intro' => [
                'sometimes',
                'boolean',
            ],
            'is_private_favorites' => [
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
            'name.required' => 'Ім\'я є обов\'язковим.',
            'name.string' => 'Ім\'я має бути рядком.',
            'name.max' => 'Ім\'я не може перевищувати 128 символів.',
            'email.required' => 'Електронна адреса є обов\'язковою.',
            'email.email' => 'Введіть коректну електронну адресу.',
            'email.max' => 'Електронна адреса не може перевищувати 128 символів.',
            'email.unique' => 'Ця електронна адреса вже використовується.',
            'password.required' => 'Пароль є обов\'язковим.',
            'password.min' => 'Пароль повинен містити щонайменше 8 символів.',
            'password.confirmed' => 'Підтвердження пароля не співпадає.',
            'gender.enum' => 'Невірне значення для статі.',
            'birthday.date' => 'Дата народження повинна бути дійсною датою.',
            'description.string' => 'Опис має бути рядком.',
            'description.max' => 'Опис не може перевищувати 512 символів.',
        ];
    }
}
