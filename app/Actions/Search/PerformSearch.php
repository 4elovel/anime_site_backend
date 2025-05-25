<?php

namespace AnimeSite\Actions\Search;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Person;
use AnimeSite\Models\Studio;
use AnimeSite\Models\Tag;
use AnimeSite\Models\Selection;
use AnimeSite\Models\Episode;
use AnimeSite\Http\Resources\AnimeResource;
use AnimeSite\Http\Resources\PersonResource;
use AnimeSite\Http\Resources\StudioResource;
use AnimeSite\Http\Resources\TagResource;
use AnimeSite\Http\Resources\SelectionResource;
use AnimeSite\Http\Resources\EpisodeResource;

class PerformSearch
{
    /**
     * Виконати пошук по всім моделям або по конкретному типу.
     *
     * @param array{
     *     query: string,
     *     type?: string|null,
     *     per_page?: int,
     *     page?: int,
     *     filters?: array,
     *     sort?: string
     * } $data
     * @return array
     */
    public function __invoke(array $data): array
    {
        $query = $data['query'];
        $type = $data['type'] ?? null;
        $perPage = $data['per_page'] ?? 15;
        $filters = $data['filters'] ?? [];
        $sort = $data['sort'] ?? 'relevance';

        // Якщо вказано конкретний тип, шукаємо тільки по ньому
        if ($type) {
            return $this->searchByType($type, $query, $perPage, $filters, $sort);
        }

        // Інакше шукаємо по всім типам
        $results = [
            'anime' => $this->searchAnime($query, 5, $filters, $sort),
            'people' => $this->searchPeople($query, 5, $filters, $sort),
            'studios' => $this->searchStudios($query, 5, $filters, $sort),
            'tags' => $this->searchTags($query, 5, $filters, $sort),
            'selections' => $this->searchSelections($query, 5, $filters, $sort),
            'episodes' => $this->searchEpisodes($query, 5, $filters, $sort),
        ];

        // Формуємо загальний результат
        return [
            'data' => [
                'anime' => [
                    'data' => AnimeResource::collection($results['anime']->items()),
                    'total' => $results['anime']->total(),
                ],
                'people' => [
                    'data' => PersonResource::collection($results['people']->items()),
                    'total' => $results['people']->total(),
                ],
                'studios' => [
                    'data' => StudioResource::collection($results['studios']->items()),
                    'total' => $results['studios']->total(),
                ],
                'tags' => [
                    'data' => TagResource::collection($results['tags']->items()),
                    'total' => $results['tags']->total(),
                ],
                'selections' => [
                    'data' => SelectionResource::collection($results['selections']->items()),
                    'total' => $results['selections']->total(),
                ],
                'episodes' => [
                    'data' => EpisodeResource::collection($results['episodes']->items()),
                    'total' => $results['episodes']->total(),
                ],
            ],
            'meta' => [
                'query' => $query,
                'total_results' => $results['anime']->total() +
                                  $results['people']->total() +
                                  $results['studios']->total() +
                                  $results['tags']->total() +
                                  $results['selections']->total() +
                                  $results['episodes']->total(),
            ],
        ];
    }

    /**
     * Пошук за конкретним типом.
     *
     * @param string $type
     * @param string $query
     * @param int $perPage
     * @param array $filters
     * @param string $sort
     * @return array
     */
    private function searchByType(string $type, string $query, int $perPage, array $filters, string $sort): array
    {
        $results = match ($type) {
            'anime' => $this->searchAnime($query, $perPage, $filters, $sort),
            'person' => $this->searchPeople($query, $perPage, $filters, $sort),
            'studio' => $this->searchStudios($query, $perPage, $filters, $sort),
            'tag' => $this->searchTags($query, $perPage, $filters, $sort),
            'selection' => $this->searchSelections($query, $perPage, $filters, $sort),
            'episode' => $this->searchEpisodes($query, $perPage, $filters, $sort),
            default => $this->searchAnime($query, $perPage, $filters, $sort),
        };

        $resourceClass = match ($type) {
            'anime' => AnimeResource::class,
            'person' => PersonResource::class,
            'studio' => StudioResource::class,
            'tag' => TagResource::class,
            'selection' => SelectionResource::class,
            'episode' => EpisodeResource::class,
            default => AnimeResource::class,
        };

        return [
            'data' => $resourceClass::collection($results->items()),
            'meta' => [
                'query' => $query,
                'type' => $type,
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
            ],
        ];
    }

    /**
     * Пошук аніме.
     *
     * @param string $query
     * @param int $perPage
     * @param array $filters
     * @param string $sort
     * @return LengthAwarePaginator
     */
    private function searchAnime(string $query, int $perPage, array $filters, string $sort): LengthAwarePaginator
    {
        $animeQuery = Anime::query();

        // Повнотекстовий пошук
        $animeQuery->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('original_title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        });

        // Застосовуємо фільтри
        if (isset($filters['status'])) {
            $animeQuery->withStatus($filters['status']);
        }

        if (isset($filters['kind'])) {
            $animeQuery->ofKind($filters['kind']);
        }

        if (isset($filters['period'])) {
            $animeQuery->ofPeriod($filters['period']);
        }

        if (isset($filters['year'])) {
            $animeQuery->whereYear('first_air_date', $filters['year']);
        }

        if (isset($filters['genre'])) {
            $animeQuery->whereHas('tags', function ($q) use ($filters) {
                $q->where('slug', $filters['genre']);
            });
        }

        if (isset($filters['studio'])) {
            $animeQuery->whereHas('studio', function ($q) use ($filters) {
                $q->where('slug', $filters['studio']);
            });
        }

        // Сортування
        switch ($sort) {
            case 'title':
                $animeQuery->orderBy('title', 'asc');
                break;
            case '-title':
                $animeQuery->orderBy('title', 'desc');
                break;
            case 'date':
                $animeQuery->orderBy('first_air_date', 'asc');
                break;
            case '-date':
                $animeQuery->orderBy('first_air_date', 'desc');
                break;
            case 'rating':
                $animeQuery->orderBy('imdb_score', 'asc');
                break;
            case '-rating':
                $animeQuery->orderBy('imdb_score', 'desc');
                break;
            default:
                // За замовчуванням сортуємо за релевантністю (спочатку точні збіги)
                $animeQuery->orderByRaw("
                    CASE
                        WHEN title LIKE ? THEN 1
                        WHEN title LIKE ? THEN 2
                        WHEN original_title LIKE ? THEN 3
                        WHEN original_title LIKE ? THEN 4
                        WHEN description LIKE ? THEN 5
                        ELSE 6
                    END
                ", [
                    $query, // Точний збіг з назвою
                    "%{$query}%", // Часткове співпадіння з назвою
                    $query, // Точний збіг з оригінальною назвою
                    "%{$query}%", // Часткове співпадіння з оригінальною назвою
                    "%{$query}%", // Часткове співпадіння з описом
                ]);
                break;
        }

        // Завантажуємо зв'язані дані
        $animeQuery->with(['studio', 'tags']);

        return $animeQuery->paginate($perPage);
    }

    /**
     * Пошук людей.
     *
     * @param string $query
     * @param int $perPage
     * @param array $filters
     * @param string $sort
     * @return LengthAwarePaginator
     */
    private function searchPeople(string $query, int $perPage, array $filters, string $sort): LengthAwarePaginator
    {
        $peopleQuery = Person::query();

        // Повнотекстовий пошук
        $peopleQuery->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('original_name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        });

        // Застосовуємо фільтри
        if (isset($filters['type'])) {
            $peopleQuery->byType($filters['type']);
        }

        if (isset($filters['gender'])) {
            $peopleQuery->byGender($filters['gender']);
        }

        // Сортування
        switch ($sort) {
            case 'title':
                $peopleQuery->orderBy('name', 'asc');
                break;
            case '-title':
                $peopleQuery->orderBy('name', 'desc');
                break;
            case 'date':
                $peopleQuery->orderBy('birth_date', 'asc');
                break;
            case '-date':
                $peopleQuery->orderBy('birth_date', 'desc');
                break;
            default:
                // За замовчуванням сортуємо за релевантністю
                $peopleQuery->orderByRaw("
                    CASE
                        WHEN name LIKE ? THEN 1
                        WHEN name LIKE ? THEN 2
                        WHEN original_name LIKE ? THEN 3
                        WHEN original_name LIKE ? THEN 4
                        WHEN description LIKE ? THEN 5
                        ELSE 6
                    END
                ", [
                    $query, // Точний збіг з ім'ям
                    "%{$query}%", // Часткове співпадіння з ім'ям
                    $query, // Точний збіг з оригінальним ім'ям
                    "%{$query}%", // Часткове співпадіння з оригінальним ім'ям
                    "%{$query}%", // Часткове співпадіння з описом
                ]);
                break;
        }

        // Завантажуємо зв'язані дані
        $peopleQuery->with(['animes']);

        return $peopleQuery->paginate($perPage);
    }

    /**
     * Пошук студій.
     *
     * @param string $query
     * @param int $perPage
     * @param array $filters
     * @param string $sort
     * @return LengthAwarePaginator
     */
    private function searchStudios(string $query, int $perPage, array $filters, string $sort): LengthAwarePaginator
    {
        $studioQuery = Studio::query();

        // Повнотекстовий пошук
        $studioQuery->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        });

        // Сортування
        switch ($sort) {
            case 'title':
                $studioQuery->orderBy('name', 'asc');
                break;
            case '-title':
                $studioQuery->orderBy('name', 'desc');
                break;
            default:
                // За замовчуванням сортуємо за релевантністю
                $studioQuery->orderByRaw("
                    CASE
                        WHEN name LIKE ? THEN 1
                        WHEN name LIKE ? THEN 2
                        WHEN description LIKE ? THEN 3
                        ELSE 4
                    END
                ", [
                    $query, // Точний збіг з назвою
                    "%{$query}%", // Часткове співпадіння з назвою
                    "%{$query}%", // Часткове співпадіння з описом
                ]);
                break;
        }

        // Завантажуємо зв'язані дані
        $studioQuery->withCount(['animes']);

        return $studioQuery->paginate($perPage);
    }

    /**
     * Пошук тегів.
     *
     * @param string $query
     * @param int $perPage
     * @param array $filters
     * @param string $sort
     * @return LengthAwarePaginator
     */
    private function searchTags(string $query, int $perPage, array $filters, string $sort): LengthAwarePaginator
    {
        $tagQuery = Tag::query();

        // Повнотекстовий пошук
        $tagQuery->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        });

        // Застосовуємо фільтри
        if (isset($filters['is_genre'])) {
            $tagQuery->where('is_genre', filter_var($filters['is_genre'], FILTER_VALIDATE_BOOLEAN));
        }

        // Сортування
        switch ($sort) {
            case 'title':
                $tagQuery->orderBy('name', 'asc');
                break;
            case '-title':
                $tagQuery->orderBy('name', 'desc');
                break;
            default:
                // За замовчуванням сортуємо за релевантністю
                $tagQuery->orderByRaw("
                    CASE
                        WHEN name LIKE ? THEN 1
                        WHEN name LIKE ? THEN 2
                        WHEN description LIKE ? THEN 3
                        ELSE 4
                    END
                ", [
                    $query, // Точний збіг з назвою
                    "%{$query}%", // Часткове співпадіння з назвою
                    "%{$query}%", // Часткове співпадіння з описом
                ]);
                break;
        }

        // Завантажуємо зв'язані дані
        $tagQuery->with(['parent', 'children']);

        return $tagQuery->paginate($perPage);
    }

    /**
     * Пошук добірок.
     *
     * @param string $query
     * @param int $perPage
     * @param array $filters
     * @param string $sort
     * @return LengthAwarePaginator
     */
    private function searchSelections(string $query, int $perPage, array $filters, string $sort): LengthAwarePaginator
    {
        $selectionQuery = Selection::query();

        // Повнотекстовий пошук
        $selectionQuery->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        });

        // Фільтруємо тільки опубліковані та активні добірки
        $selectionQuery->where('is_published', true)
                      ->where('is_active', true);

        // Сортування
        switch ($sort) {
            case 'title':
                $selectionQuery->orderBy('title', 'asc');
                break;
            case '-title':
                $selectionQuery->orderBy('title', 'desc');
                break;
            case 'date':
                $selectionQuery->orderBy('created_at', 'asc');
                break;
            case '-date':
                $selectionQuery->orderBy('created_at', 'desc');
                break;
            default:
                // За замовчуванням сортуємо за релевантністю
                $selectionQuery->orderByRaw("
                    CASE
                        WHEN title LIKE ? THEN 1
                        WHEN title LIKE ? THEN 2
                        WHEN description LIKE ? THEN 3
                        ELSE 4
                    END
                ", [
                    $query, // Точний збіг з назвою
                    "%{$query}%", // Часткове співпадіння з назвою
                    "%{$query}%", // Часткове співпадіння з описом
                ]);
                break;
        }

        // Завантажуємо зв'язані дані
        $selectionQuery->with(['user']);

        return $selectionQuery->paginate($perPage);
    }

    /**
     * Пошук епізодів.
     *
     * @param string $query
     * @param int $perPage
     * @param array $filters
     * @param string $sort
     * @return LengthAwarePaginator
     */
    private function searchEpisodes(string $query, int $perPage, array $filters, string $sort): LengthAwarePaginator
    {
        $episodeQuery = Episode::query();

        // Повнотекстовий пошук
        $episodeQuery->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        });

        // Сортування
        switch ($sort) {
            case 'title':
                $episodeQuery->orderBy('title', 'asc');
                break;
            case '-title':
                $episodeQuery->orderBy('title', 'desc');
                break;
            case 'date':
                $episodeQuery->orderBy('air_date', 'asc');
                break;
            case '-date':
                $episodeQuery->orderBy('air_date', 'desc');
                break;
            default:
                // За замовчуванням сортуємо за релевантністю
                $episodeQuery->orderByRaw("
                    CASE
                        WHEN title LIKE ? THEN 1
                        WHEN title LIKE ? THEN 2
                        WHEN description LIKE ? THEN 3
                        ELSE 4
                    END
                ", [
                    $query, // Точний збіг з назвою
                    "%{$query}%", // Часткове співпадіння з назвою
                    "%{$query}%", // Часткове співпадіння з описом
                ]);
                break;
        }

        // Завантажуємо зв'язані дані
        $episodeQuery->with(['anime']);

        return $episodeQuery->paginate($perPage);
    }
}
