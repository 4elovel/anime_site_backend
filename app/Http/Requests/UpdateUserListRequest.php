<?php
namespace AnimeSite\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use AnimeSite\Enums\UserListType;

class UpdateUserListRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'ulid',
                'exists:user_lists,id', // Перевірка, що запис існує
            ],
            'listable_id' => [
                'required',
                'ulid',
            ],
            'listable_type' => [
                'required',
                'string',
            ],
            'type' => [
                'required',
                Rule::enum(UserListType::class),
            ],
        ];
    }

    /**
     * Кастомні повідомлення для помилок валідації.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'ID запису списку є обов’язковим.',
            'id.ulid' => 'ID запису списку має бути у форматі ULID.',
            'id.exists' => 'Вказаний запис списку не існує.',
            'listable_id.required' => 'ID об’єкта списку є обов’язковим.',
            'listable_id.ulid' => 'ID об’єкта списку має бути у форматі ULID.',
            'listable_type.required' => 'Тип об’єкта списку є обов’язковим.',
            'listable_type.string' => 'Тип об’єкта списку має бути рядком.',
            'type.required' => 'Тип списку є обов’язковим.',
            'type.enum' => 'Невалідне значення для типу списку.',
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
