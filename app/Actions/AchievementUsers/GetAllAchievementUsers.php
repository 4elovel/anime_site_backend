<?php

namespace AnimeSite\Actions\AchievementUsers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\AchievementUser;

class GetAllAchievementUsers
{
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', AchievementUser::class);

        $perPage = (int) $request->input('per_page', 15);

        return AchievementUser::query()
            ->when($request->filled('user_id'), fn($q) =>
            $q->where('user_id', $request->input('user_id'))
            )
            ->when($request->filled('achievement_id'), fn($q) =>
            $q->where('achievement_id', $request->input('achievement_id'))
            )
            ->paginate($perPage);
    }
}
