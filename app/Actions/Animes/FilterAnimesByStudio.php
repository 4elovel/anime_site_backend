<?php

namespace AnimeSite\Actions\Animes;

use Illuminate\Database\Eloquent\Builder;

class FilterAnimesByStudio
{
    /**
     * Застосувати фільтрацію за студіями.
     *
     * @param Builder $query
     * @param array<string> $studioIds
     * @return Builder
     */
    public function __invoke(Builder $query, array $studioIds): Builder
    {
        if (empty($studioIds)) {
            return $query;
        }

        return $query->whereIn('studio_id', $studioIds);
    }
}
