<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\Currency;
use AnimeSite\Enums\TariffFeature;

class StoreTariffRequest extends FormRequest
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
                'required',
                'string',
                'max:128',
                'unique:tariffs,slug', // Унікальність slug
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
            'price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'currency' => [
                'required',
                Rule::enum(Currency::class),
            ],
            'duration_days' => [
                'required',
                'integer',
                'min:1',
            ],
            'features' => [
                'nullable',
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
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'slug.required' => 'Слаг є обов\'язковим.',
            'slug.string' => 'Слаг має бути рядком.',
            'slug.max' => 'Слаг не може перевищувати 128 символів.',
            'slug.unique' => 'Цей слаг вже використовується.',
            'name.required' => 'Назва є обов\'язковою.',
            'name.string' => 'Назва має бути рядком.',
            'name.max' => 'Назва не може перевищувати 128 символів.',
            'description.required' => 'Опис є обов\'язковим.',
            'description.string' => 'Опис має бути рядком.',
            'description.max' => 'Опис не може перевищувати 512 символів.',
            'price.required' => 'Ціна є обов\'язковою.',
            'price.numeric' => 'Ціна має бути числом.',
            'price.min' => 'Ціна не може бути від\'ємною.',
            'currency.required' => 'Валюта є обов\'язковою.',
            'currency.enum' => 'Невірне значення для валюти.',
            'duration_days.required' => 'Тривалість є обов\'язковою.',
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
