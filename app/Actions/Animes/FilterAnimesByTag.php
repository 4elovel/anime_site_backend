<?php

namespace AnimeSite\Actions\Animes;

use Illuminate\Database\Eloquent\Builder;

class FilterAnimesByTag
{
    /**
     * Застосувати фільтрацію за тегами.
     *
     * @param Builder $query
     * @param array<string> $tagIds
     * @return Builder
     */
    public function __invoke(Builder $query, array $tagIds): Builder
    {
        if (empty($tagIds)) {
            return $query;
        }

        return $query->whereHas('tags', function ($q) use ($tagIds) {
            $q->whereIn('tags.id', $tagIds);
        });
    }
}
