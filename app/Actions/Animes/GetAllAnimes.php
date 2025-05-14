<?php

namespace AnimeSite\Actions\Animes;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Anime;

class GetAllAnimes
{
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Anime::class);

        $perPage = (int) $request->input('per_page', 15);

        return Anime::query()
            ->when($request->filled('kind'), fn($q) =>
            $q->ofKind($request->input('kind'))
            )
            ->when($request->filled('status'), fn($q) =>
            $q->withStatus($request->input('status'))
            )
            ->when($request->filled('period'), fn($q) =>
            $q->ofPeriod($request->input('period'))
            )
            ->when($request->filled('imdb_score'), fn($q) =>
            $q->withImdbScoreGreaterThan($request->input('imdb_score'))
            )
            ->when($request->filled('country'), fn($q) =>
            $q->fromCountry($request->input('country'))
            )
            ->paginate($perPage);
    }
}
