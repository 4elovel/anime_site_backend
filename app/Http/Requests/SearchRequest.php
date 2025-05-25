<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Пошук доступний всім користувачам
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'query' => [
                'required',
                'string',
                'min:2',
                'max:248',
            ],
            'type' => [
                'sometimes',
                'string',
                'in:anime,person,studio,tag,selection,episode',
            ],
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:50',
            ],
            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'filters' => [
                'sometimes',
                'array',
            ],
            'filters.status' => [
                'sometimes',
                'string',
                'in:announced,ongoing,released,finished',
            ],
            'filters.kind' => [
                'sometimes',
                'string',
                'in:tv,movie,ova,ona,special,music',
            ],
            'filters.period' => [
                'sometimes',
                'string',
                'in:winter,spring,summer,fall',
            ],
            'filters.year' => [
                'sometimes',
                'integer',
                'min:1950',
                'max:' . (date('Y') + 1),
            ],
            'filters.genre' => [
                'sometimes',
                'string',
            ],
            'filters.studio' => [
                'sometimes',
                'string',
            ],
            'sort' => [
                'sometimes',
                'string',
                'in:relevance,title,-title,date,-date,rating,-rating',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Пошуковий запит є обов\'язковим.',
            'query.string' => 'Пошуковий запит має бути рядком.',
            'query.min' => 'Пошуковий запит має містити щонайменше 2 символи.',
            'query.max' => 'Пошуковий запит не може перевищувати 248 символів.',
            'type.in' => 'Невірний тип пошуку. Допустимі значення: anime, person, studio, tag, selection, episode.',
            'per_page.integer' => 'Кількість елементів на сторінці має бути цілим числом.',
            'per_page.min' => 'Кількість елементів на сторінці має бути не менше 1.',
            'per_page.max' => 'Кількість елементів на сторінці не може перевищувати 50.',
            'page.integer' => 'Номер сторінки має бути цілим числом.',
            'page.min' => 'Номер сторінки має бути не менше 1.',
            'filters.array' => 'Фільтри мають бути масивом.',
            'filters.status.in' => 'Невірний статус. Допустимі значення: announced, ongoing, released, finished.',
            'filters.kind.in' => 'Невірний тип аніме. Допустимі значення: tv, movie, ova, ona, special, music.',
            'filters.period.in' => 'Невірний сезон. Допустимі значення: winter, spring, summer, fall.',
            'filters.year.integer' => 'Рік має бути цілим числом.',
            'filters.year.min' => 'Рік має бути не менше 1950.',
            'filters.year.max' => 'Рік не може перевищувати ' . (date('Y') + 1) . '.',
            'sort.in' => 'Невірний параметр сортування. Допустимі значення: relevance, title, -title, date, -date, rating, -rating.',
        ];
    }
}
