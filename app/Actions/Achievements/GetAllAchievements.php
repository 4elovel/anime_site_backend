<?php

namespace AnimeSite\Actions\Achievements;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Achievement;

class GetAllAchievements
{
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Achievement::class);

        $perPage = (int) $request->input('per_page', 15);

        return Achievement::query()
            ->when($request->filled('search'), fn($q) =>
            $q->where('name', 'like', '%'.$request->input('search').'%')
            )
            ->paginate($perPage);
    }
}
