<?php

namespace AnimeSite\Actions\UserLists;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserList;

class GetAllUserLists
{
    /**
     * Отримати список списків користувачів з фільтрацією та пагінацією.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', UserList::class);

        $perPage = (int) $request->input('per_page', 15);

        return UserList::query()
            ->when($request->filled('user_id'), fn($q) =>
            $q->where('user_id', $request->input('user_id'))
            )
            ->when($request->filled('listable_type'), fn($q) =>
            $q->where('listable_type', $request->input('listable_type'))
            )
            ->when($request->filled('type'), fn($q) =>
            $q->where('type', $request->input('type'))
            )
            ->paginate($perPage);
    }
}
