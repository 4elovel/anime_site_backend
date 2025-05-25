<?php

namespace AnimeSite\Actions\Search;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\SearchHistory;
use AnimeSite\Models\User;

class GetUserSearchHistory
{
    /**
     * Отримати історію пошуку користувача з пагінацією.
     *
     * @param User $user
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(User $user, Request $request): LengthAwarePaginator
    {
        // Перевіряємо права доступу
        Gate::authorize('viewSearchHistory', $user);

        $perPage = (int) $request->input('per_page', 15);

        return SearchHistory::query()
            ->where('user_id', $user->id)
            ->when($request->filled('query'), fn($q) =>
                $q->where('query', 'like', '%' . $request->input('query') . '%')
            )
            ->orderBy('updated_at', 'desc') // Сортуємо за датою оновлення (останні запити спочатку)
            ->paginate($perPage);
    }
}
