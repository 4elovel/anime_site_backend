<?php

namespace AnimeSite\Actions\Studios;

use Illuminate\Database\Eloquent\Builder;

class SearchStudios
{
    /**
     * Застосувати пошук до запиту студій.
     *
     * @param Builder $query
     * @param string $searchTerm
     * @return Builder
     */
    public function __invoke(Builder $query, string $searchTerm): Builder
    {
        // Якщо доступний повнотекстовий пошук, використовуємо його
        if (config('app.fulltext_search_enabled', false)) {
            return $query->fullTextSearch($searchTerm);
        }

        // Інакше використовуємо звичайний пошук по назві
        return $query->byName($searchTerm);
    }
}
