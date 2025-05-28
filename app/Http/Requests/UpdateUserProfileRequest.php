<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\Gender;

class UpdateUserProfileRequest extends FormRequest
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
            'name' => [
                'sometimes',
                'string',
                'max:128',
            ],
            'gender' => [
                'sometimes',
                'nullable',
                Rule::enum(Gender::class),
            ],
            'birthday' => [
                'sometimes',
                'nullable',
                'date',
            ],
            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:512',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Ім\'я має бути рядком.',
            'name.max' => 'Ім\'я не може перевищувати 128 символів.',
            'gender.enum' => 'Невірне значення для статі.',
            'birthday.date' => 'Дата народження повинна бути дійсною датою.',
            'description.string' => 'Опис має бути рядком.',
            'description.max' => 'Опис не може перевищувати 512 символів.',
        ];
    }
}
