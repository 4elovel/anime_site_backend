<?php

namespace AnimeSite\Actions\Animes;

use Illuminate\Database\Eloquent\Builder;

class FilterAnimesByPerson
{
    /**
     * Застосувати фільтрацію за людьми (актори, режисери тощо).
     *
     * @param Builder $query
     * @param array<string> $personIds
     * @return Builder
     */
    public function __invoke(Builder $query, array $personIds): Builder
    {
        if (empty($personIds)) {
            return $query;
        }

        return $query->whereHas('people', function ($q) use ($personIds) {
            $q->whereIn('people.id', $personIds);
        });
    }
}
