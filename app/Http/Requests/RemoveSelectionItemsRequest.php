<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RemoveSelectionItemsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'animes' => ['sometimes', 'array'],
            'animes.*' => ['required', 'string', 'exists:animes,id'],
            'persons' => ['sometimes', 'array'],
            'persons.*' => ['required', 'string', 'exists:people,id'],
            'episodes' => ['sometimes', 'array'],
            'episodes.*' => ['required', 'string', 'exists:episodes,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'animes' => 'аніме',
            'animes.*' => 'аніме',
            'persons' => 'персонажі',
            'persons.*' => 'персонаж',
            'episodes' => 'епізоди',
            'episodes.*' => 'епізод',
        ];
    }
}
