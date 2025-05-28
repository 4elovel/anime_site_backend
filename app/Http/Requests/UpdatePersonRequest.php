<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\Gender;
use AnimeSite\Enums\PersonType;

class UpdatePersonRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'ulid',
                'exists:people,id',
            ],
            'slug' => [
                'required',
                'string',
                'max:128',
                Rule::unique('people', 'slug')->ignore($this->id), // Унікальність з ігноруванням поточного запису
            ],
            'name' => [
                'required',
                'string',
                'max:128',
            ],
            'original_name' => [
                'nullable',
                'string',
                'max:128',
            ],
            'image' => [
                'nullable',
                'string',
                'max:2048',
            ],
            'description' => [
                'nullable',
                'string',
                'max:512',
            ],
            'birthday' => [
                'nullable',
                'date',
            ],
            'birthplace' => [
                'nullable',
                'string',
                'max:248',
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
            'type' => [
                'required',
                Rule::enum(PersonType::class),
            ],
            'gender' => [
                'nullable',
                Rule::enum(Gender::class),
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'ID особи є обов’язковим.',
            'id.ulid' => 'ID особи має бути у форматі ULID.',
            'id.exists' => 'Вказана особа не існує.',
            'slug.required' => 'Слаг є обов’язковим.',
            'slug.string' => 'Слаг має бути рядком.',
            'slug.max' => 'Слаг не може перевищувати 128 символів.',
            'slug.unique' => 'Цей слаг уже зайнятий.',
            'name.required' => 'Ім’я є обов’язковим.',
            'name.string' => 'Ім’я має бути рядком.',
            'name.max' => 'Ім’я не може перевищувати 128 символів.',
            'original_name.string' => 'Оригінальне ім’я має бути рядком.',
            'original_name.max' => 'Оригінальне ім’я не може перевищувати 128 символів.',
            'image.string' => 'Зображення має бути рядком.',
            'image.max' => 'Зображення не може перевищувати 2048 символів.',
            'description.string' => 'Опис має бути рядком.',
            'description.max' => 'Опис не може перевищувати 512 символів.',
            'birthday.date' => 'Дата народження має бути у форматі дати.',
            'birthplace.string' => 'Місце народження має бути рядком.',
            'birthplace.max' => 'Місце народження не може перевищувати 248 символів.',
            'meta_title.string' => 'Мета-заголовок має бути рядком.',
            'meta_title.max' => 'Мета-заголовок не може перевищувати 128 символів.',
            'meta_description.string' => 'Мета-опис має бути рядком.',
            'meta_description.max' => 'Мета-опис не може перевищувати 376 символів.',
            'meta_image.string' => 'Мета-зображення має бути рядком.',
            'meta_image.max' => 'Мета-зображення не може перевищувати 2048 символів.',
            'type.required' => 'Тип особи є обов’язковим.',
            'type.enum' => 'Невалідне значення для типу особи.',
            'gender.enum' => 'Невалідне значення для статі.',
        ];
    }
}
