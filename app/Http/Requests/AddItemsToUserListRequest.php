<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\UserListType;

class AddItemsToUserListRequest extends FormRequest
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
            'type' => [
                'required',
                Rule::enum(UserListType::class),
            ],
            'listable_type' => [
                'required',
                'string',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*' => [
                'required',
                'ulid',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Тип списку є обов\'язковим.',
            'type.enum' => 'Невалідне значення для типу списку.',
            'listable_type.required' => 'Тип об\'єкта списку є обов\'язковим.',
            'listable_type.string' => 'Тип об\'єкта списку має бути рядком.',
            'items.required' => 'Список об\'єктів є обов\'язковим.',
            'items.array' => 'Список об\'єктів має бути масивом.',
            'items.min' => 'Список об\'єктів має містити щонайменше один елемент.',
            'items.*.required' => 'ID об\'єкта є обов\'язковим.',
            'items.*.ulid' => 'ID об\'єкта має бути у форматі ULID.',
        ];
    }
}
