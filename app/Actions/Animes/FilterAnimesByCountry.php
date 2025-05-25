<?php

namespace AnimeSite\Actions\Animes;

use AnimeSite\Enums\Country;
use Illuminate\Database\Eloquent\Builder;

class FilterAnimesByCountry
{
    /**
     * Застосувати фільтрацію за країнами виробництва.
     *
     * @param Builder $query
     * @param array<Country> $countries
     * @return Builder
     */
    public function __invoke(Builder $query, array $countries): Builder
    {
        if (empty($countries)) {
            return $query;
        }

        return $query->where(function ($q) use ($countries) {
            foreach ($countries as $country) {
                $q->orWhereJsonContains('countries', $country);
            }
        });
    }
}
