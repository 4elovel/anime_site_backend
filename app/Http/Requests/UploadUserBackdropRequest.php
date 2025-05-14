<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadUserBackdropRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'backdrop' => [
                'required',
                'image',
                'max:10240', // 10MB
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'backdrop.required' => 'Файл фону є обов\'язковим.',
            'backdrop.image' => 'Файл повинен бути зображенням.',
            'backdrop.max' => 'Розмір файлу не може перевищувати 10MB.',
        ];
    }
}
