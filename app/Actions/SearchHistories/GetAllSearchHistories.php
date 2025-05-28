<?php

namespace AnimeSite\Actions\SearchHistories;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\SearchHistory;

class GetAllSearchHistories
{
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', SearchHistory::class);

        $perPage = (int) $request->input('per_page', 15);

        return SearchHistory::query()
            ->when($request->filled('user_id'), fn($q) =>
            $q->where('user_id', $request->input('user_id'))
            )
            ->when($request->filled('query'), fn($q) =>
            $q->where('query', 'like', '%' . $request->input('query') . '%')
            )
            ->paginate($perPage);
    }
}
