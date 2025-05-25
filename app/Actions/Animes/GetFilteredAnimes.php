<?php

namespace AnimeSite\Actions\Animes;

use AnimeSite\DTOs\Animes\AnimeIndexDTO;
use AnimeSite\Models\Anime;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class GetFilteredAnimes
{
    /**
     * Отримати список аніме з фільтрацією, пошуком, сортуванням та пагінацією.
     *
     * @param AnimeIndexDTO $dto
     * @return LengthAwarePaginator
     */
    public function __invoke(AnimeIndexDTO $dto): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Anime::class);

        // Починаємо з базового запиту
        $query = Anime::query();

        // Застосовуємо пошук, якщо вказано пошуковий запит
        if ($dto->query) {
            // Якщо доступний повнотекстовий пошук, використовуємо його
            if (config('app.fulltext_search_enabled', false)) {
                $query->fullTextSearch($dto->query);
            } else {
                // Інакше використовуємо звичайний пошук по ключовим полям
                $query->search($dto->query, ['title', 'original_title', 'description']);
            }
        }

        // Застосовуємо фільтри
        $this->applyFilters($query, $dto);

        // Застосовуємо сортування
        $this->applySorting($query, $dto);

        // Завантажуємо зв'язані дані
        $query->with(['studio', 'tags', 'ratings']);

        // Додаємо середній рейтинг користувачів
        $query->withAvg('ratings', 'number');

        // Повертаємо результати з пагінацією
        return $query->paginate(
            perPage: $dto->perPage,
            page: $dto->page
        );
    }

    /**
     * Застосувати всі фільтри до запиту
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param AnimeIndexDTO $dto
     * @return void
     */
    private function applyFilters($query, AnimeIndexDTO $dto): void
    {
        // Фільтрація за типами аніме
        if ($dto->kinds) {
            $query->where(function ($q) use ($dto) {
                foreach ($dto->kinds as $kind) {
                    $q->orWhere('kind', $kind);
                }
            });
        }

        // Фільтрація за статусами
        if ($dto->statuses) {
            $query->where(function ($q) use ($dto) {
                foreach ($dto->statuses as $status) {
                    $q->orWhere('status', $status);
                }
            });
        }

        // Фільтрація за сезонами
        if ($dto->periods) {
            $query->where(function ($q) use ($dto) {
                foreach ($dto->periods as $period) {
                    $q->orWhere('period', $period);
                }
            });
        }

        // Фільтрація за рейтингом IMDB
        if ($dto->minScore !== null) {
            $query->where('imdb_score', '>=', $dto->minScore);
        }

        if ($dto->maxScore !== null) {
            $query->where('imdb_score', '<=', $dto->maxScore);
        }

        // Фільтрація за країнами
        if ($dto->countries) {
            $query->where(function ($q) use ($dto) {
                foreach ($dto->countries as $country) {
                    $q->orWhereJsonContains('countries', $country);
                }
            });
        }

        // Фільтрація за студіями
        if ($dto->studioIds) {
            $query->whereIn('studio_id', $dto->studioIds);
        }

        // Фільтрація за тегами
        if ($dto->tagIds) {
            $query->whereHas('tags', function ($q) use ($dto) {
                $q->whereIn('tags.id', $dto->tagIds);
            });
        }

        // Фільтрація за людьми
        if ($dto->personIds) {
            $query->whereHas('people', function ($q) use ($dto) {
                $q->whereIn('people.id', $dto->personIds);
            });
        }

        // Фільтрація за роком випуску
        if ($dto->minYear !== null) {
            $query->whereYear('first_air_date', '>=', $dto->minYear);
        }

        if ($dto->maxYear !== null) {
            $query->whereYear('first_air_date', '<=', $dto->maxYear);
        }

        // Фільтрація за кількістю епізодів
        if ($dto->minEpisodesCount !== null) {
            $query->where('episodes_count', '>=', $dto->minEpisodesCount);
        }

        if ($dto->maxEpisodesCount !== null) {
            $query->where('episodes_count', '<=', $dto->maxEpisodesCount);
        }

        // Фільтрація за тривалістю
        if ($dto->minDuration !== null) {
            $query->where('duration', '>=', $dto->minDuration);
        }

        if ($dto->maxDuration !== null) {
            $query->where('duration', '<=', $dto->maxDuration);
        }

        // Фільтрація за віковими обмеженнями
        if ($dto->restrictedRatings) {
            $query->where(function ($q) use ($dto) {
                foreach ($dto->restrictedRatings as $rating) {
                    $q->orWhere('restricted_rating', $rating);
                }
            });
        }

        // Фільтрація за джерелами
        if ($dto->sources) {
            $query->where(function ($q) use ($dto) {
                foreach ($dto->sources as $source) {
                    $q->orWhere('source', $source);
                }
            });
        }

        // Фільтрація за якістю відео
        if ($dto->videoQualities) {
            $query->where(function ($q) use ($dto) {
                foreach ($dto->videoQualities as $quality) {
                    $q->orWhere('video_quality', $quality);
                }
            });
        }

        // Фільтрація за популярністю
        if ($dto->popular) {
            $minRatings = $dto->minRatings ?? 10;
            $query->popular($minRatings);
        }

        // Фільтрація за найвищим рейтингом
        if ($dto->topRated) {
            $minTopScore = $dto->minTopScore ?? 7.0;
            $query->topRated($minTopScore);
        }

        // Фільтрація за нещодавно доданими
        if ($dto->recentlyAdded) {
            $days = $dto->days ?? 30;
            $query->addedInLastDays($days);
        }

        // Фільтрація за нещодавно оновленими
        if ($dto->recentlyUpdated) {
            $days = $dto->days ?? 30;
            $query->updatedInLastDays($days);
        }

        // Фільтрація за схожими аніме
        if ($dto->similarTo) {
            $query->similarTo($dto->similarTo);
        }

        // Фільтрація за пов'язаними аніме
        if ($dto->relatedTo) {
            $query->relatedTo($dto->relatedTo);
        }

        // Фільтрація за активністю
        if ($dto->isActive !== null) {
            $query->active($dto->isActive);
        }

        // Фільтрація за публікацією
        if ($dto->isPublished !== null) {
            $query->published($dto->isPublished);
        }
    }

    /**
     * Застосувати сортування до запиту
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param AnimeIndexDTO $dto
     * @return void
     */
    private function applySorting($query, AnimeIndexDTO $dto): void
    {
        $sort = $dto->sort ?? 'created_at';
        $direction = $dto->direction ?? 'desc';

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
    }
}
