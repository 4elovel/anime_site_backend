<?php

namespace AnimeSite\Actions\Studios;

use Illuminate\Database\Eloquent\Builder;

class FilterStudiosByAnimeCount
{
    /**
     * Застосувати фільтрацію за мінімальною кількістю аніме.
     *
     * @param Builder $query
     * @param int $minCount
     * @return Builder
     */
    public function __invoke(Builder $query, int $minCount): Builder
    {
        return $query->withMinAnimeCount($minCount);
    }
}
