<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\CommentReportType;

class UpdateCommentReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment_id' => [
                'sometimes',
                'ulid',
                'exists:comments,id',
            ],
            'type' => [
                'sometimes',
                Rule::enum(CommentReportType::class),
            ],
            'body' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'is_viewed' => [
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
            'comment_id.ulid' => 'ID коментаря має бути у форматі ULID.',
            'comment_id.exists' => 'Вказаний коментар не існує.',
            'type.enum' => 'Невірний тип репорту.',
            'body.string' => 'Опис має бути текстовим.',
            'body.max' => 'Опис не може перевищувати 1000 символів.',
            'is_viewed.boolean' => 'Поле "is_viewed" має бути true або false.',
        ];
    }

    /**
     * Підготовка даних перед валідацією.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => auth()->id(), // Захищаємо від зміни
        ]);
    }
}
