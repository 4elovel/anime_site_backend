<?php

namespace AnimeSite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\Currency;
use AnimeSite\Enums\PaymentStatus;

class StorePaymentRequest extends FormRequest
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
            'tariff_id' => [
                'required',
                'ulid',
                'exists:tariffs,id', // Перевірка, що тариф існує
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01', // Мінімальна сума платежу
            ],
            'currency' => [
                'required',
                Rule::enum(Currency::class),
            ],
            'payment_method' => [
                'required',
                'string',
                'max:50',
            ],
            'return_url' => [
                'nullable',
                'url',
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'tariff_id.required' => 'ID тарифу є обов\'язковим.',
            'tariff_id.ulid' => 'ID тарифу має бути у форматі ULID.',
            'tariff_id.exists' => 'Вказаний тариф не існує.',
            'amount.required' => 'Сума платежу є обов\'язковою.',
            'amount.numeric' => 'Сума платежу має бути числом.',
            'amount.min' => 'Сума платежу має бути не менше 0.01.',
            'currency.required' => 'Валюта є обов\'язковою.',
            'currency.enum' => 'Невірне значення для валюти.',
            'payment_method.required' => 'Спосіб оплати є обов\'язковим.',
            'payment_method.string' => 'Спосіб оплати має бути рядком.',
            'payment_method.max' => 'Спосіб оплати не може перевищувати 50 символів.',
            'return_url.url' => 'URL для повернення має бути дійсним URL.',
        ];
    }

    /**
     * Підготовка даних перед валідацією.
     */
    protected function prepareForValidation(): void
    {
        // Додаємо user_id автоматично з авторизованого користувача
        $this->merge([
            'user_id' => auth()->id(),
        ]);
    }
}
