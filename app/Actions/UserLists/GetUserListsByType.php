<?php

namespace AnimeSite\Actions\UserLists;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Enums\UserListType;
use AnimeSite\Models\UserList;

class GetUserListsByType
{
    /**
     * Отримати списки користувачів за типом з пагінацією та фільтрацією.
     *
     * @param string $type
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(string $type, Request $request): LengthAwarePaginator
    {
        // Перевіряємо, чи тип є валідним значенням з енума
        if (!UserListType::tryFrom($type)) {
            abort(400, 'Невірний тип списку');
        }

        $perPage = (int) $request->input('per_page', 15);

        return UserList::query()
            // Фільтрація за типом списку
            ->where('type', $type)
            // Фільтрація за користувачем
            ->when($request->filled('user_id'), fn($q) =>
                $q->where('user_id', $request->input('user_id'))
            , fn($q) =>
                // Якщо user_id не вказано, показуємо списки поточного користувача
                $q->where('user_id', auth()->id())
            )
            // Фільтрація за типом об'єкта
            ->when($request->filled('listable_type'), fn($q) =>
                $q->where('listable_type', $request->input('listable_type'))
            )
            // Пошук за назвою об'єкта (через зв'язок)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                return $query->whereHasMorph('listable', '*', function ($q) use ($search) {
                    $q->where(function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('title', 'like', "%{$search}%");
                    });
                });
            })
            // Сортування
            ->when($request->filled('sort'), function ($query) use ($request) {
                $sort = $request->input('sort');
                $direction = 'asc';
                
                if (str_starts_with($sort, '-')) {
                    $direction = 'desc';
                    $sort = substr($sort, 1);
                }
                
                if (in_array($sort, ['created_at', 'updated_at'])) {
                    $query->orderBy($sort, $direction);
                }
            }, fn($q) =>
                $q->orderBy('created_at', 'desc') // За замовчуванням сортуємо за датою створення (нові спочатку)
            )
            // Завантажуємо зв'язані дані
            ->with(['listable', 'user'])
            // Пагінація
            ->paginate($perPage);
    }
}
