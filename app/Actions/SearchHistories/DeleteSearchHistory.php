<?php

namespace AnimeSite\Actions\SearchHistories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\SearchHistory;

class DeleteSearchHistory
{
    public function __invoke(SearchHistory $searchHistory): void
    {
        Gate::authorize('delete', $searchHistory);
        $searchHistory->delete();
    }
}
