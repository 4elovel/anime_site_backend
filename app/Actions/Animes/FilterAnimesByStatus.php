<?php

namespace AnimeSite\Actions\Animes;

use AnimeSite\Enums\Status;
use Illuminate\Database\Eloquent\Builder;

class FilterAnimesByStatus
{
    /**
     * Застосувати фільтрацію за статусами аніме.
     *
     * @param Builder $query
     * @param array<Status> $statuses
     * @return Builder
     */
    public function __invoke(Builder $query, array $statuses): Builder
    {
        if (empty($statuses)) {
            return $query;
        }

        return $query->where(function ($q) use ($statuses) {
            foreach ($statuses as $status) {
                $q->orWhere('status', $status);
            }
        });
    }
}
