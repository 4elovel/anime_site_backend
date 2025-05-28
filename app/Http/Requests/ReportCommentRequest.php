<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\CommentReportType;

class ReportCommentRequest extends FormRequest
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
                Rule::enum(CommentReportType::class),
            ],
            'body' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Тип скарги є обов\'язковим.',
            'type.enum' => 'Невірний тип скарги.',
            'body.string' => 'Опис має бути текстовим.',
            'body.max' => 'Опис не може перевищувати 1000 символів.',
        ];
    }
}
