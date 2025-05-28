<?php

namespace AnimeSite\Actions\Ratings;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Rating;

class GetAllRatings
{
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Rating::class);

        $perPage = (int) $request->input('per_page', 15);

        return Rating::query()
            ->when($request->filled('user_id'), fn($q) =>
            $q->where('user_id', $request->input('user_id'))
            )
            ->when($request->filled('anime_id'), fn($q) =>
            $q->where('anime_id', $request->input('anime_id'))
            )
            ->when($request->filled('min_rating') && $request->filled('max_rating'), fn($q) =>
            $q->whereBetween('number', [
                $request->input('min_rating'),
                $request->input('max_rating')
            ])
            )
            ->paginate($perPage);
    }
}
