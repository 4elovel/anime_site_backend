<?php

namespace AnimeSite\Actions\Studios;

use Illuminate\Database\Eloquent\Builder;

class FilterStudiosByAnimeScore
{
    /**
     * Застосувати фільтрацію за мінімальним рейтингом аніме.
     *
     * @param Builder $query
     * @param float $minScore
     * @return Builder
     */
    public function __invoke(Builder $query, float $minScore): Builder
    {
        return $query->producedHighRatedAnime($minScore);
    }
}
