<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\Currency;
use AnimeSite\Enums\TariffFeature;

class UpdateTariffRequest extends FormRequest
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
            'slug' => [
                'sometimes',
                'string',
                'max:128',
                Rule::unique('tariffs', 'slug')->ignore($this->tariff),
            ],
            'name' => [
                'sometimes',
                'string',
                'max:128',
            ],
            'description' => [
                'sometimes',
                'string',
                'max:512',
            ],
            'price' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
            'currency' => [
                'sometimes',
                Rule::enum(Currency::class),
            ],
            'duration_days' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'features' => [
                'sometimes',
                'array',
            ],
            'features.*' => [
                'string',
                Rule::enum(TariffFeature::class),
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'meta_title' => [
                'sometimes',
                'nullable',
                'string',
                'max:128',
            ],
            'meta_description' => [
                'sometimes',
                'nullable',
                'string',
                'max:376',
            ],
            'meta_image' => [
                'sometimes',
                'nullable',
                'string',
                'max:2048',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'slug.string' => 'Слаг має бути рядком.',
            'slug.max' => 'Слаг не може перевищувати 128 символів.',
            'slug.unique' => 'Цей слаг вже використовується.',
            'name.string' => 'Назва має бути рядком.',
            'name.max' => 'Назва не може перевищувати 128 символів.',
            'description.string' => 'Опис має бути рядком.',
            'description.max' => 'Опис не може перевищувати 512 символів.',
            'price.numeric' => 'Ціна має бути числом.',
            'price.min' => 'Ціна не може бути від\'ємною.',
            'currency.enum' => 'Невірне значення для валюти.',
            'duration_days.integer' => 'Тривалість має бути цілим числом.',
            'duration_days.min' => 'Тривалість має бути не менше 1 дня.',
            'features.array' => 'Функції мають бути масивом.',
            'features.*.enum' => 'Невірне значення для функції.',
            'is_active.boolean' => 'Значення активності має бути true або false.',
            'meta_title.string' => 'Мета-заголовок має бути рядком.',
            'meta_title.max' => 'Мета-заголовок не може перевищувати 128 символів.',
            'meta_description.string' => 'Мета-опис має бути рядком.',
            'meta_description.max' => 'Мета-опис не може перевищувати 376 символів.',
            'meta_image.string' => 'Мета-зображення має бути рядком.',
            'meta_image.max' => 'Шлях до мета-зображення не може перевищувати 2048 символів.',
        ];
    }
}
