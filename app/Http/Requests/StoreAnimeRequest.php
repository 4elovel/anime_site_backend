<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\Kind;
use AnimeSite\Enums\Period;
use AnimeSite\Enums\RestrictedRating;
use AnimeSite\Enums\Source;
use AnimeSite\Enums\Status;

class StoreAnimeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'api_sources' => [
                'nullable',
                'json', // Перевірка, що це валідний JSON
            ],
            'slug' => [
                'required',
                'string',
                'max:128',
                'unique:animes,slug', // Унікальність slug
            ],
            'name' => [
                'required',
                'string',
                'max:248',
            ],
            'description' => [
                'required',
                'string',
            ],
            'image_name' => [
                'required',
                'string',
                'max:2048',
            ],
            'aliases' => [
                'nullable',
                'json',
            ],
            'studio_id' => [
                'required',
                'ulid',
                'exists:studios,id', // Перевірка, що студія існує
            ],
            'countries' => [
                'nullable',
                'json',
            ],
            'poster' => [
                'nullable',
                'string',
                'max:2048',
            ],
            'duration' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'episodes_count' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'first_air_date' => [
                'nullable',
                'date',
            ],
            'last_air_date' => [
                'nullable',
                'date',
                'after_or_equal:first_air_date', // Перевірка, що дата завершення не раніше початку
            ],
            'imdb_score' => [
                'nullable',
                'numeric',
                'min:0',
                'max:10', // Оцінка IMDB від 0 до 10
            ],
            'attachments' => [
                'nullable',
                'json',
            ],
            'related' => [
                'nullable',
                'json',
            ],
            'similars' => [
                'nullable',
                'json',
            ],
            'is_published' => [
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
            'kind' => [
                'required',
                Rule::enum(Kind::class), // Перевірка на валідне значення енума
            ],
            'status' => [
                'required',
                Rule::enum(Status::class),
            ],
            'period' => [
                'nullable',
                Rule::enum(Period::class),
            ],
            'restricted_rating' => [
                'required',
                Rule::enum(RestrictedRating::class),
            ],
            'source' => [
                'required',
                Rule::enum(Source::class),
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'api_sources.json' => 'Поле "API джерела" має бути валідним JSON.',
            'slug.required' => 'Слаг є обов’язковим.',
            'slug.string' => 'Слаг має бути рядком.',
            'slug.max' => 'Слаг не може перевищувати 128 символів.',
            'slug.unique' => 'Цей слаг уже зайнятий.',
            'name.required' => 'Назва є обов’язковою.',
            'name.string' => 'Назва має бути рядком.',
            'name.max' => 'Назва не може перевищувати 248 символів.',
            'description.required' => 'Опис є обов’язковим.',
            'description.string' => 'Опис має бути рядком.',
            'image_name.required' => 'Назва зображення є обов’язковою.',
            'image_name.string' => 'Назва зображення має бути рядком.',
            'image_name.max' => 'Назва зображення не може перевищувати 2048 символів.',
            'aliases.json' => 'Поле "аліаси" має бути валідним JSON.',
            'studio_id.required' => 'ID студії є обов’язковим.',
            'studio_id.ulid' => 'ID студії має бути у форматі ULID.',
            'studio_id.exists' => 'Вказана студія не існує.',
            'countries.json' => 'Поле "країни" має бути валідним JSON.',
            'poster.string' => 'Постер має бути рядком.',
            'poster.max' => 'Постер не може перевищувати 2048 символів.',
            'duration.integer' => 'Тривалість має бути цілим числом.',
            'duration.min' => 'Тривалість не може бути від’ємною.',
            'episodes_count.integer' => 'Кількість епізодів має бути цілим числом.',
            'episodes_count.min' => 'Кількість епізодів не може бути від’ємною.',
            'first_air_date.date' => 'Дата початку ефіру має бути у форматі дати.',
            'last_air_date.date' => 'Дата завершення ефіру має бути у форматі дати.',
            'last_air_date.after_or_equal' => 'Дата завершення не може бути раніше дати початку.',
            'imdb_score.numeric' => 'Оцінка IMDB має бути числом.',
            'imdb_score.min' => 'Оцінка IMDB не може бути меншою за 0.',
            'imdb_score.max' => 'Оцінка IMDB не може перевищувати 10.',
            'attachments.json' => 'Поле "вкладення" має бути валідним JSON.',
            'related.json' => 'Поле "пов’язані" має бути валідним JSON.',
            'similars.json' => 'Поле "схожі" має бути валідним JSON.',
            'is_published.boolean' => 'Поле "опубліковано" має бути true або false.',
            'meta_title.string' => 'Мета-заголовок має бути рядком.',
            'meta_title.max' => 'Мета-заголовок не може перевищувати 128 символів.',
            'meta_description.string' => 'Мета-опис має бути рядком.',
            'meta_description.max' => 'Мета-опис не може перевищувати 376 символів.',
            'meta_image.string' => 'Мета-зображення має бути рядком.',
            'meta_image.max' => 'Мета-зображення не може перевищувати 2048 символів.',
            'kind.required' => 'Тип аніме є обов’язковим.',
            'kind.enum' => 'Невалідне значення для типу аніме.',
            'status.required' => 'Статус є обов’язковим.',
            'status.enum' => 'Невалідне значення для статусу.',
            'period.enum' => 'Невалідне значення для періоду.',
            'restricted_rating.required' => 'Віковий рейтинг є обов’язковим.',
            'restricted_rating.enum' => 'Невалідне значення для вікового рейтингу.',
            'source.required' => 'Джерело є обов’язковим.',
            'source.enum' => 'Невалідне значення для джерела.',
        ];
    }
}
