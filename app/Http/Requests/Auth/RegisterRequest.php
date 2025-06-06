<?php

namespace AnimeSite\Http\Requests\Auth;

use AnimeSite\DTOs\Auth\RegisterDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    /**
     * Convert the validated data to a DTO.
     */
    public function toDTO(): RegisterDTO
    {
        return RegisterDTO::fromArray($this->validated());
    }
}