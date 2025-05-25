<?php

namespace AnimeSite\DTOs\Studios;

use AnimeSite\Enums\Kind;
use Illuminate\Http\Request;

class StudioIndexDTO
{
    /**
     * @param string|null $query Пошуковий запит
     * @param bool|null $isActive Фільтрувати за активністю
     * @param bool|null $isPublished Фільтрувати за публікацією
     * @param int|null $minAnimeCount Мінімальна кількість аніме
     * @param Kind[]|null $animeKinds Типи аніме, які продюсувала студія
     * @param float|null $minAnimeScore Мінімальний рейтинг аніме, які продюсувала студія
     * @param int|null $animeYear Рік випуску аніме, які продюсувала студія
     * @param bool|null $popular Фільтрувати за популярністю
     * @param bool|null $recentlyAdded Нещодавно додані
     * @param int|null $days Кількість днів для нещодавно доданих
     * @param string|null $sort Поле для сортування
     * @param string|null $direction Напрямок сортування (asc/desc)
     * @param int $perPage Кількість елементів на сторінці
     * @param int $page Номер сторінки
     */
    public function __construct(
        public readonly ?string $query = null,
        public readonly ?bool $isActive = null,
        public readonly ?bool $isPublished = null,
        public readonly ?int $minAnimeCount = null,
        public readonly ?array $animeKinds = null,
        public readonly ?float $minAnimeScore = null,
        public readonly ?int $animeYear = null,
        public readonly ?bool $popular = null,
        public readonly ?bool $recentlyAdded = null,
        public readonly ?int $days = null,
        public readonly ?string $sort = null,
        public readonly ?string $direction = null,
        public readonly int $perPage = 15,
        public readonly int $page = 1,
    ) {
    }

    /**
     * Створити DTO з HTTP запиту
     *
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): self
    {
        // Обробка типів аніме
        $animeKinds = null;
        if ($request->filled('anime_kinds')) {
            $animeKinds = array_map(
                fn($kind) => Kind::tryFrom($kind),
                is_array($request->input('anime_kinds')) ? $request->input('anime_kinds') : [$request->input('anime_kinds')]
            );
            $animeKinds = array_filter($animeKinds); // Видаляємо null значення
        }

        // Обробка напрямку сортування
        $direction = $request->input('direction');
        if ($direction && !in_array(strtolower($direction), ['asc', 'desc'])) {
            $direction = 'desc'; // За замовчуванням desc, якщо передано невірне значення
        }

        // Обробка поля сортування
        $sort = $request->input('sort');
        $allowedSortFields = [
            'name', 'created_at', 'updated_at', 'animes_count'
        ];
        if ($sort && !in_array($sort, $allowedSortFields)) {
            $sort = 'created_at'; // За замовчуванням created_at, якщо передано невірне значення
        }

        return new self(
            query: $request->input('search'),
            isActive: $request->filled('is_active') ? filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN) : null,
            isPublished: $request->filled('is_published') ? filter_var($request->input('is_published'), FILTER_VALIDATE_BOOLEAN) : null,
            minAnimeCount: $request->filled('min_anime_count') ? (int) $request->input('min_anime_count') : null,
            animeKinds: $animeKinds,
            minAnimeScore: $request->filled('min_anime_score') ? (float) $request->input('min_anime_score') : null,
            animeYear: $request->filled('anime_year') ? (int) $request->input('anime_year') : null,
            popular: $request->boolean('popular'),
            recentlyAdded: $request->boolean('recently_added'),
            days: $request->filled('days') ? (int) $request->input('days') : null,
            sort: $sort,
            direction: $direction,
            perPage: $request->filled('per_page') ? (int) $request->input('per_page') : 15,
            page: $request->filled('page') ? (int) $request->input('page') : 1,
        );
    }
}
