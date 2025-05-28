<?php

namespace AnimeSite\Actions\Animes;

use Illuminate\Database\Eloquent\Builder;

class SearchAnimes
{
    /**
     * Застосувати пошук до запиту аніме.
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

        // Інакше використовуємо звичайний пошук по ключовим полям
        return $query->search($searchTerm, ['title', 'original_title', 'description']);
    }
}
