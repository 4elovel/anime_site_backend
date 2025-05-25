<?php

namespace AnimeSite\Actions\Animes;

use AnimeSite\Enums\Kind;
use Illuminate\Database\Eloquent\Builder;

class FilterAnimesByKind
{
    /**
     * Застосувати фільтрацію за типами аніме.
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

        return $query->where(function ($q) use ($kinds) {
            foreach ($kinds as $kind) {
                $q->orWhere('kind', $kind);
            }
        });
    }
}
