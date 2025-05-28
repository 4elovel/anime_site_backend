<?php

namespace AnimeSite\Actions\SearchHistories;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\SearchHistory;

class ShowSearchHistory
{
    public function __invoke(SearchHistory $searchHistory): SearchHistory
    {
        Gate::authorize('view', $searchHistory);
        return $searchHistory;
    }
}
