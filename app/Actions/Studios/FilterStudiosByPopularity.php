<?php

namespace AnimeSite\Actions\Studios;

use Illuminate\Database\Eloquent\Builder;

class FilterStudiosByPopularity
{
    /**
     * Застосувати фільтрацію за популярністю.
     *
     * @param Builder $query
     * @param int $minAnimes
     * @return Builder
     */
    public function __invoke(Builder $query, int $minAnimes = 5): Builder
    {
        return $query->popular($minAnimes);
    }
}
