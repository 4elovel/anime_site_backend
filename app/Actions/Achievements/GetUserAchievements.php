<?php

namespace AnimeSite\Actions\Achievements;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\User;
use AnimeSite\Models\Achievement;

class GetUserAchievements
{
    /**
     * Отримати досягнення користувача з пагінацією.
     *
     * @param User $user
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(User $user, Request $request): LengthAwarePaginator
    {
        Gate::authorize('view', $user);

        $perPage = (int) $request->input('per_page', 15);

        return $user->achievements()
            ->when($request->filled('search'), fn($q) =>
                $q->where('name', 'like', '%' . $request->input('search') . '%')
            )
            ->when($request->filled('sort'), function ($query) use ($request) {
                $sort = $request->input('sort');
                $direction = 'asc';
                
                if (str_starts_with($sort, '-')) {
                    $direction = 'desc';
                    $sort = substr($sort, 1);
                }
                
                if (in_array($sort, ['name', 'max_counts'])) {
                    $query->orderBy($sort, $direction);
                } elseif ($sort === 'progress') {
                    $query->orderBy('progress_count', $direction);
                }
            })
            ->paginate($perPage);
    }
}
