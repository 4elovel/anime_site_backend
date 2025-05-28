<?php

namespace AnimeSite\Actions\Studios;

use Illuminate\Database\Eloquent\Builder;

class FilterStudiosByAnimeYear
{
    /**
     * Застосувати фільтрацію за роком випуску аніме.
     *
     * @param Builder $query
     * @param int $year
     * @return Builder
     */
    public function __invoke(Builder $query, int $year): Builder
    {
        return $query->producedAnimeInYear($year);
    }
}
