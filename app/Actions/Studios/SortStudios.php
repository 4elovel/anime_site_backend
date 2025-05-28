<?php

namespace AnimeSite\Actions\Studios;

use Illuminate\Database\Eloquent\Builder;

class SortStudios
{
    /**
     * Застосувати сортування до запиту студій.
     *
     * @param Builder $query
     * @param string|null $sort
     * @param string|null $direction
     * @return Builder
     */
    public function __invoke(Builder $query, ?string $sort, ?string $direction): Builder
    {
        $sort = $sort ?? 'created_at';
        $direction = $direction ?? 'desc';

        switch ($sort) {
            case 'name':
                $query->orderBy('name', $direction);
                break;
            case 'animes_count':
                $query->orderByAnimeCount($direction);
                break;
            case 'created_at':
                $query->orderByCreatedAt($direction);
                break;
            case 'updated_at':
                $query->orderByUpdatedAt($direction);
                break;
            default:
                // За замовчуванням сортуємо за датою створення (нові спочатку)
                $query->orderByCreatedAt('desc');
                break;
        }

        return $query;
    }
}
