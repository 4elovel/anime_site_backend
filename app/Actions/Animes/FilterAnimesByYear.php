<?php

namespace AnimeSite\Actions\Animes;

use Illuminate\Database\Eloquent\Builder;

class FilterAnimesByYear
{
    /**
     * Застосувати фільтрацію за роком випуску.
     *
     * @param Builder $query
     * @param int|null $minYear
     * @param int|null $maxYear
     * @return Builder
     */
    public function __invoke(Builder $query, ?int $minYear, ?int $maxYear): Builder
    {
        if ($minYear !== null) {
            $query->whereYear('first_air_date', '>=', $minYear);
        }

        if ($maxYear !== null) {
            $query->whereYear('first_air_date', '<=', $maxYear);
        }

        return $query;
    }
}
