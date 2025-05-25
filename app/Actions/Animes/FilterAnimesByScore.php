<?php

namespace AnimeSite\Actions\Animes;

use Illuminate\Database\Eloquent\Builder;

class FilterAnimesByScore
{
    /**
     * Застосувати фільтрацію за рейтингом IMDB.
     *
     * @param Builder $query
     * @param float|null $minScore
     * @param float|null $maxScore
     * @return Builder
     */
    public function __invoke(Builder $query, ?float $minScore, ?float $maxScore): Builder
    {
        if ($minScore !== null) {
            $query->where('imdb_score', '>=', $minScore);
        }

        if ($maxScore !== null) {
            $query->where('imdb_score', '<=', $maxScore);
        }

        return $query;
    }
}
