<?php

namespace AnimeSite\Actions\Animes;

use AnimeSite\Enums\Period;
use Illuminate\Database\Eloquent\Builder;

class FilterAnimesByPeriod
{
    /**
     * Застосувати фільтрацію за сезонами аніме.
     *
     * @param Builder $query
     * @param array<Period> $periods
     * @return Builder
     */
    public function __invoke(Builder $query, array $periods): Builder
    {
        if (empty($periods)) {
            return $query;
        }

        return $query->where(function ($q) use ($periods) {
            foreach ($periods as $period) {
                $q->orWhere('period', $period);
            }
        });
    }
}
