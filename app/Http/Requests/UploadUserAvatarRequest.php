<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadUserAvatarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'avatar' => [
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
            'avatar.required' => 'Файл аватарки є обов\'язковим.',
            'avatar.image' => 'Файл повинен бути зображенням.',
            'avatar.max' => 'Розмір файлу не може перевищувати 10MB.',
        ];
    }
}
