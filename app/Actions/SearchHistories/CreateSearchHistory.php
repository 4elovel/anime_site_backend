<?php

namespace AnimeSite\Actions\SearchHistories;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\SearchHistory;

class CreateSearchHistory
{
    /**
     * @param array{
     *     user_id: string,
     *     query: string
     * } $data
     */
    public function __invoke(array $data): SearchHistory
    {
        Gate::authorize('create', SearchHistory::class);

        return SearchHistory::create($data);
    }
}
