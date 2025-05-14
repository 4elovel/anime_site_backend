<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEpisodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'ulid', // Перевірка, що це дійсний ULID
                'exists:episodes,id', // Перевірка, що епізод існує
            ],
            'anime_id' => [
                'required',
                'ulid',
                'exists:animes,id',
            ],
            'number' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
            ],
            'slug' => [
                'required',
                'string',
                'max:128',
                Rule::unique('episodes', 'slug')->ignore($this->id), // Унікальність з ігноруванням поточного епізоду
            ],
            'name' => [
                'required',
                'string',
                'max:128',
            ],
            'description' => [
                'nullable',
                'string',
                'max:512',
            ],
            'duration' => [
                'nullable',
                'integer',
                'min:0',
                'max:65535',
            ],
            'air_date' => [
                'nullable',
                'date',
            ],
            'is_filler' => [
                'nullable',
                'boolean',
            ],
            'pictures' => [
                'nullable',
                'json',
            ],
            'video_players' => [
                'nullable',
                'json',
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
            'id.required' => 'ID епізоду є обов’язковим.',
            'id.ulid' => 'ID епізоду має бути у форматі ULID.',
            'id.exists' => 'Вказаний епізод не існує.',
            'anime_id.required' => 'ID аніме є обов’язковим.',
            'anime_id.ulid' => 'ID аніме має бути у форматі ULID.',
            'anime_id.exists' => 'Вказане аніме не існує.',
            'number.required' => 'Номер епізоду є обов’язковим.',
            'number.integer' => 'Номер епізоду має бути цілим числом.',
            'number.min' => 'Номер епізоду має бути не меншим за 1.',
            'number.max' => 'Номер епізоду не може перевищувати 65535.',
            'slug.required' => 'Слаг є обов’язковим.',
            'slug.string' => 'Слаг має бути рядком.',
            'slug.max' => 'Слаг не може перевищувати 128 символів.',
            'slug.unique' => 'Цей слаг уже зайнятий.',
            'name.required' => 'Назва епізоду є обов’язковою.',
            'name.string' => 'Назва має бути рядком.',
            'name.max' => 'Назва не може перевищувати 128 символів.',
            'description.string' => 'Опис має бути рядком.',
            'description.max' => 'Опис не може перевищувати 512 символів.',
            'duration.integer' => 'Тривалість має бути цілим числом.',
            'duration.max' => 'Тривалість не може перевищувати 65535 хвилин.',
            'air_date.date' => 'Дата виходу має бути у форматі дати.',
            'is_filler.boolean' => 'Поле "філер" має бути true або false.',
            'pictures.json' => 'Поле "зображення" має бути валідним JSON.',
            'video_players.json' => 'Поле "відеоплеєри" має бути валідним JSON.',
            'meta_title.string' => 'Мета-заголовок має бути рядком.',
            'meta_title.max' => 'Мета-заголовок не може перевищувати 128 символів.',
            'meta_description.string' => 'Мета-опис має бути рядком.',
            'meta_description.max' => 'Мета-опис не може перевищувати 376 символів.',
            'meta_image.string' => 'Мета-зображення має бути рядком.',
            'meta_image.max' => 'Мета-зображення не може перевищувати 2048 символів.',
        ];
    }
}
