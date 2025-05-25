<?php

namespace AnimeSite\Actions\UserLists;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;
use AnimeSite\Models\UserList;

class GetUserLists
{
    /**
     * Отримати списки конкретного користувача з пагінацією та фільтрацією.
     *
     * @param User $user
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(User $user, Request $request): LengthAwarePaginator
    {
        // Перевіряємо права доступу
        Gate::authorize('view', $user);

        $perPage = (int) $request->input('per_page', 15);

        return UserList::query()
            // Фільтрація за користувачем
            ->where('user_id', $user->id)
            // Фільтрація за типом списку
            ->when($request->filled('type'), fn($q) =>
                $q->where('type', $request->input('type'))
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
                
                if (in_array($sort, ['created_at', 'updated_at', 'type'])) {
                    $query->orderBy($sort, $direction);
                }
            }, fn($q) =>
                $q->orderBy('created_at', 'desc') // За замовчуванням сортуємо за датою створення (нові спочатку)
            )
            // Завантажуємо зв'язані дані
            ->with(['listable'])
            // Пагінація
            ->paginate($perPage);
    }
}
