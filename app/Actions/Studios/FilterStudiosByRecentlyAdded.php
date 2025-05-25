<?php

namespace AnimeSite\Actions\Studios;

use Illuminate\Database\Eloquent\Builder;

class FilterStudiosByRecentlyAdded
{
    /**
     * Застосувати фільтрацію за нещодавно доданими.
     *
     * @param Builder $query
     * @param int $days
     * @return Builder
     */
    public function __invoke(Builder $query, int $days = 30): Builder
    {
        return $query->addedInLastDays($days);
    }
}
