<?php

namespace AnimeSite\Actions\Animes;

use Illuminate\Database\Eloquent\Builder;

class SortAnimes
{
    /**
     * Застосувати сортування до запиту аніме.
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
            case 'title':
                $query->orderBy('title', $direction);
                break;
            case 'original_title':
                $query->orderBy('original_title', $direction);
                break;
            case 'imdb_score':
                $query->orderBy('imdb_score', $direction);
                break;
            case 'first_air_date':
                $query->orderBy('first_air_date', $direction);
                break;
            case 'last_air_date':
                $query->orderBy('last_air_date', $direction);
                break;
            case 'episodes_count':
                $query->orderBy('episodes_count', $direction);
                break;
            case 'duration':
                $query->orderBy('duration', $direction);
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
