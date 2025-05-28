<?php

namespace AnimeSite\Actions\People;

use AnimeSite\DTOs\People\PersonIndexDTO;
use AnimeSite\Models\Person;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class GetFilteredPeople
{
    /**
     * Отримати список людей з фільтрацією, пошуком, сортуванням та пагінацією.
     *
     * @param PersonIndexDTO $dto
     * @return LengthAwarePaginator
     */
    public function __invoke(PersonIndexDTO $dto): LengthAwarePaginator
    {
        // Gate::authorize('viewAny', Person::class); // Дозволяємо перегляд персон без авторизації

        // Починаємо з базового запиту
        $query = Person::query();

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
        $query->with(['animes', 'tags']);

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
     * @param PersonIndexDTO $dto
     * @return void
     */
    private function applyFilters($query, PersonIndexDTO $dto): void
    {
        // Фільтрація за типами людей
        if ($dto->types) {
            $query->where(function ($q) use ($dto) {
                foreach ($dto->types as $type) {
                    $q->orWhere('type', $type);
                }
            });
        }

        // Фільтрація за статями
        if ($dto->genders) {
            $query->where(function ($q) use ($dto) {
                foreach ($dto->genders as $gender) {
                    $q->orWhere('gender', $gender);
                }
            });
        }

        // Фільтрація за активністю
        if ($dto->isActive !== null) {
            $query->active($dto->isActive);
        }

        // Фільтрація за публікацією
        if ($dto->isPublished !== null) {
            $query->published($dto->isPublished);
        }

        // Фільтрація за місцем народження
        if ($dto->birthplace) {
            $query->fromBirthplace($dto->birthplace);
        }

        // Фільтрація за роком народження
        if ($dto->birthYear !== null) {
            $query->bornInYear($dto->birthYear);
        }

        // Фільтрація за віковим діапазоном
        if ($dto->minAge !== null && $dto->maxAge !== null) {
            $query->withAgeRange($dto->minAge, $dto->maxAge);
        }

        // Фільтрація за аніме
        if ($dto->animeId) {
            $query->inAnime($dto->animeId);
        }

        // Фільтрація за ім'ям персонажа
        if ($dto->characterName) {
            $query->playedCharacter($dto->characterName);
        }

        // Фільтрація за актором озвучення
        if ($dto->voicePersonId) {
            $query->voicedBy($dto->voicePersonId);
        }

        // Фільтрація за добіркою
        if ($dto->selectionId) {
            $query->inSelection($dto->selectionId);
        }

        // Фільтрація за популярністю
        if ($dto->popular) {
            $minAnimes = $dto->minAnimes ?? 3;
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
     * @param PersonIndexDTO $dto
     * @return void
     */
    private function applySorting($query, PersonIndexDTO $dto): void
    {
        $sort = $dto->sort ?? 'created_at';
        $direction = $dto->direction ?? 'desc';

        switch ($sort) {
            case 'name':
                $query->orderBy('name', $direction);
                break;
            case 'original_name':
                $query->orderBy('original_name', $direction);
                break;
            case 'birthday':
                $query->orderBy('birthday', $direction);
                break;
            case 'created_at':
                $query->orderByCreatedAt($direction);
                break;
            case 'updated_at':
                $query->orderByUpdatedAt($direction);
                break;
            case 'popularity':
                $query->orderByPopularity();
                break;
            default:
                // За замовчуванням сортуємо за датою створення (нові спочатку)
                $query->orderByCreatedAt('desc');
                break;
        }
    }
}
