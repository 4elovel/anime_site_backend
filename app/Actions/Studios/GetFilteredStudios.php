<?php

namespace AnimeSite\Actions\Studios;

use AnimeSite\DTOs\Studios\StudioIndexDTO;
use AnimeSite\Models\Studio;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class GetFilteredStudios
{
    /**
     * Отримати список студій з фільтрацією, пошуком, сортуванням та пагінацією.
     *
     * @param StudioIndexDTO $dto
     * @return LengthAwarePaginator
     */
    public function __invoke(StudioIndexDTO $dto): LengthAwarePaginator
    {
        // Gate::authorize('viewAny', Studio::class); // Дозволяємо перегляд студій без авторизації

        // Починаємо з базового запиту
        $query = Studio::query();

        // Застосовуємо пошук, якщо вказано пошуковий запит
        if ($dto->query) {
            // Якщо доступний повнотекстовий пошук, використовуємо його
            if (config('app.fulltext_search_enabled', false)) {
                $query->fullTextSearch($dto->query);
            } else {
                // Інакше використовуємо звичайний пошук по назві
                $query->byName($dto->query);
            }
        }

        // Застосовуємо фільтри
        $this->applyFilters($query, $dto);

        // Застосовуємо сортування
        $this->applySorting($query, $dto);

        // Завантажуємо зв'язані дані
        $query->with(['animes']);

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
     * @param StudioIndexDTO $dto
     * @return void
     */
    private function applyFilters($query, StudioIndexDTO $dto): void
    {
        // Фільтрація за активністю
        if ($dto->isActive !== null) {
            $query->active($dto->isActive);
        }

        // Фільтрація за публікацією
        if ($dto->isPublished !== null) {
            $query->published($dto->isPublished);
        }

        // Фільтрація за мінімальною кількістю аніме
        if ($dto->minAnimeCount !== null) {
            $query->withMinAnimeCount($dto->minAnimeCount);
        }

        // Фільтрація за типами аніме, які продюсувала студія
        if ($dto->animeKinds) {
            foreach ($dto->animeKinds as $kind) {
                $query->producedAnimeOfKind($kind->value);
            }
        }

        // Фільтрація за мінімальним рейтингом аніме
        if ($dto->minAnimeScore !== null) {
            $query->producedHighRatedAnime($dto->minAnimeScore);
        }

        // Фільтрація за роком випуску аніме
        if ($dto->animeYear !== null) {
            $query->producedAnimeInYear($dto->animeYear);
        }

        // Фільтрація за популярністю
        if ($dto->popular) {
            $minAnimes = $dto->minAnimeCount ?? 5;
            $query->popular($minAnimes);
        }

        // Фільтрація за нещодавно доданими
        if ($dto->recentlyAdded) {
            $days = $dto->days ?? 30;
            $query->addedInLastDays($days);
        }
    }

    /**
     * Застосувати сортування до запиту
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param StudioIndexDTO $dto
     * @return void
     */
    private function applySorting($query, StudioIndexDTO $dto): void
    {
        $sort = $dto->sort ?? 'created_at';
        $direction = $dto->direction ?? 'desc';

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
    }
}
