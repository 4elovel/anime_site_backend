<?php

namespace AnimeSite\Actions\SearchHistories;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\SearchHistory;

class UpdateSearchHistory
{
    /**
     * Оновлює існуючий запис історії пошуку.
     *
     * @param SearchHistory $searchHistory
     * @param array{
     *     query: string
     * } $data
     */
    public function __invoke(SearchHistory $searchHistory, array $data): SearchHistory
    {
        Gate::authorize('update', $searchHistory);

        // Оновлення полів
        $searchHistory->update($data);

        return $searchHistory;
    }
}
