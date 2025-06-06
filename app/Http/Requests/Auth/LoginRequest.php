<?php

namespace AnimeSite\Http\Requests\Auth;

use AnimeSite\DTOs\Auth\LoginDTO;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Convert the validated data to a DTO.
     */
    public function toDTO(): LoginDTO
    {
        return LoginDTO::fromArray($this->validated());
    }
}