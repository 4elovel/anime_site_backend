<?php

namespace AnimeSite\Actions\Episodes;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Episode;

class GetAllEpisodes
{
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Episode::class);

        $perPage = (int) $request->input('per_page', 15);

        return Episode::query()
            ->when($request->filled('anime_id'), fn($q) =>
            $q->where('anime_id', $request->input('anime_id'))
            )
            ->when($request->filled('air_date'), fn($q) =>
            $q->where('air_date', $request->input('air_date'))
            )
            ->when($request->has('is_filler'), fn($q) =>
            $q->where('is_filler', $request->input('is_filler'))
            )
            ->when($request->filled('slug'), fn($q) =>
            $q->where('slug', 'LIKE', "%{$request->input('slug')}%")
            )
            ->paginate($perPage);
    }
}
