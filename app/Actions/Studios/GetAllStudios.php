<?php

namespace AnimeSite\Actions\Studios;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Studio;

class GetAllStudios
{
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Studio::class);

        $perPage = (int) $request->input('per_page', 15);

        return Studio::query()
            ->when($request->filled('search'), fn($q) =>
            $q->search($request->input('search'))
            )
            ->with(['animes'])
            ->paginate($perPage);
    }
}
