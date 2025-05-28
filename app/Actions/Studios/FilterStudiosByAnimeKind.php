<?php

namespace AnimeSite\Actions\Studios;

use AnimeSite\Enums\Kind;
use Illuminate\Database\Eloquent\Builder;

class FilterStudiosByAnimeKind
{
    /**
     * Застосувати фільтрацію за типами аніме, які продюсувала студія.
     *
     * @param Builder $query
     * @param array<Kind> $kinds
     * @return Builder
     */
    public function __invoke(Builder $query, array $kinds): Builder
    {
        if (empty($kinds)) {
            return $query;
        }

        foreach ($kinds as $kind) {
            $query->producedAnimeOfKind($kind->value);
        }

        return $query;
    }
}
