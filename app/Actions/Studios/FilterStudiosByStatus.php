<?php

namespace AnimeSite\Actions\Studios;

use Illuminate\Database\Eloquent\Builder;

class FilterStudiosByStatus
{
    /**
     * Застосувати фільтрацію за активністю та публікацією.
     *
     * @param Builder $query
     * @param bool|null $isActive
     * @param bool|null $isPublished
     * @return Builder
     */
    public function __invoke(Builder $query, ?bool $isActive, ?bool $isPublished): Builder
    {
        if ($isActive !== null) {
            $query->active($isActive);
        }

        if ($isPublished !== null) {
            $query->published($isPublished);
        }

        return $query;
    }
}
