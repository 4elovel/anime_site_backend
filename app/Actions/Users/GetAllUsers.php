<?php

namespace AnimeSite\Actions\Users;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;

class GetAllUsers
{
    /**
     * Отримати список користувачів з фільтрацією та пагінацією.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', User::class);

        $perPage = (int) $request->input('per_page', 15);

        return User::query()
            ->when($request->filled('role'), fn($q) =>
            $q->where('role', $request->input('role'))
            )
            ->when($request->filled('is_vip'), fn($q) =>
            $q->where('vip', filter_var($request->input('is_vip'), FILTER_VALIDATE_BOOLEAN))
            )
            ->paginate($perPage);
    }
}
