<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'ulid',
                'exists:tags,id', // Перевірка, що тег існує
            ],
            'slug' => [
                'required',
                'string',
                'max:128',
                Rule::unique('tags', 'slug')->ignore($this->id), // Унікальність з ігноруванням поточного запису
            ],
            'name' => [
                'required',
                'string',
                'max:128',
            ],
            'description' => [
                'required',
                'string',
                'max:512',
            ],
            'image' => [
                'nullable',
                'string',
                'max:2048',
            ],
            'aliases' => [
                'nullable',
                'json',
            ],
            'is_genre' => [
                'nullable',
                'boolean',
            ],
            'meta_title' => [
                'nullable',
                'string',
                'max:128',
            ],
            'meta_description' => [
                'nullable',
                'string',
                'max:376',
            ],
            'meta_image' => [
                'nullable',
                'string',
                'max:2048',
            ],
            'parent_id' => [
                'nullable',
                'ulid',
                'exists:tags,id',
                Rule::notIn($this->id), // Перевірка, що тег не є своїм власним батьківським тегом
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'ID тегу є обов’язковим.',
            'id.ulid' => 'ID тегу має бути у форматі ULID.',
            'id.exists' => 'Вказаний тег не існує.',
            'slug.required' => 'Слаг є обов’язковим.',
            'slug.string' => 'Слаг має бути рядком.',
            'slug.max' => 'Слаг не може перевищувати 128 символів.',
            'slug.unique' => 'Цей слаг уже зайнятий.',
            'name.required' => 'Назва є обов’язковою.',
            'name.string' => 'Назва має бути рядком.',
            'name.max' => 'Назва не може перевищувати 128 символів.',
            'description.required' => 'Опис є обов’язковим.',
            'description.string' => 'Опис має бути рядком.',
            'description.max' => 'Опис не може перевищувати 512 символів.',
            'image.string' => 'Зображення має бути рядком.',
            'image.max' => 'Зображення не може перевищувати 2048 символів.',
            'aliases.json' => 'Поле "аліаси" має бути валідним JSON.',
            'is_genre.boolean' => 'Поле "жанр" має бути true або false.',
            'meta_title.string' => 'Мета-заголовок має бути рядком.',
            'meta_title.max' => 'Мета-заголовок не може перевищувати 128 символів.',
            'meta_description.string' => 'Мета-опис має бути рядком.',
            'meta_description.max' => 'Мета-опис не може перевищувати 376 символів.',
            'meta_image.string' => 'Мета-зображення має бути рядком.',
            'meta_image.max' => 'Мета-зображення не може перевищувати 2048 символів.',
            'parent_id.ulid' => 'ID батьківського тегу має бути у форматі ULID.',
            'parent_id.exists' => 'Вказаний батьківський тег не існує.',
            'parent_id.not_in' => 'Тег не може бути своїм власним батьківським тегом.',
        ];
    }
}
