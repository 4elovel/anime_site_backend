<?php

namespace AnimeSite\DTOs\People;

use AnimeSite\Enums\Gender;
use AnimeSite\Enums\PersonType;
use Illuminate\Http\Request;

class PersonIndexDTO
{
    /**
     * @param string|null $query Пошуковий запит
     * @param PersonType[]|null $types Типи людей
     * @param Gender[]|null $genders Статі
     * @param bool|null $isActive Фільтрувати за активністю
     * @param bool|null $isPublished Фільтрувати за публікацією
     * @param string|null $birthplace Місце народження
     * @param int|null $birthYear Рік народження
     * @param int|null $minAge Мінімальний вік
     * @param int|null $maxAge Максимальний вік
     * @param string|null $animeId ID аніме, в якому бере участь людина
     * @param string|null $characterName Ім'я персонажа, якого грає людина
     * @param string|null $voicePersonId ID актора озвучення
     * @param string|null $selectionId ID добірки, в якій є людина
     * @param bool|null $popular Фільтрувати за популярністю
     * @param int|null $minAnimes Мінімальна кількість аніме для популярних
     * @param bool|null $recentlyAdded Нещодавно додані
     * @param int|null $days Кількість днів для нещодавно доданих
     * @param string|null $sort Поле для сортування
     * @param string|null $direction Напрямок сортування (asc/desc)
     * @param int $perPage Кількість елементів на сторінці
     * @param int $page Номер сторінки
     */
    public function __construct(
        public readonly ?string $query = null,
        public readonly ?array $types = null,
        public readonly ?array $genders = null,
        public readonly ?bool $isActive = null,
        public readonly ?bool $isPublished = null,
        public readonly ?string $birthplace = null,
        public readonly ?int $birthYear = null,
        public readonly ?int $minAge = null,
        public readonly ?int $maxAge = null,
        public readonly ?string $animeId = null,
        public readonly ?string $characterName = null,
        public readonly ?string $voicePersonId = null,
        public readonly ?string $selectionId = null,
        public readonly ?bool $popular = null,
        public readonly ?int $minAnimes = null,
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
        // Обробка типів людей
        $types = null;
        if ($request->filled('types')) {
            $types = array_map(
                fn($type) => PersonType::tryFrom($type),
                is_array($request->input('types')) ? $request->input('types') : [$request->input('types')]
            );
            $types = array_filter($types); // Видаляємо null значення
        }

        // Обробка статей
        $genders = null;
        if ($request->filled('genders')) {
            $genders = array_map(
                fn($gender) => Gender::tryFrom($gender),
                is_array($request->input('genders')) ? $request->input('genders') : [$request->input('genders')]
            );
            $genders = array_filter($genders); // Видаляємо null значення
        }

        // Обробка напрямку сортування
        $direction = $request->input('direction');
        if ($direction && !in_array(strtolower($direction), ['asc', 'desc'])) {
            $direction = 'desc'; // За замовчуванням desc, якщо передано невірне значення
        }

        // Обробка поля сортування
        $sort = $request->input('sort');
        $allowedSortFields = [
            'name', 'original_name', 'birthday', 'created_at', 'updated_at', 'popularity'
        ];
        if ($sort && !in_array($sort, $allowedSortFields)) {
            $sort = 'created_at'; // За замовчуванням created_at, якщо передано невірне значення
        }

        return new self(
            query: $request->input('search'),
            types: $types,
            genders: $genders,
            isActive: $request->filled('is_active') ? filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN) : null,
            isPublished: $request->filled('is_published') ? filter_var($request->input('is_published'), FILTER_VALIDATE_BOOLEAN) : null,
            birthplace: $request->input('birthplace'),
            birthYear: $request->filled('birth_year') ? (int) $request->input('birth_year') : null,
            minAge: $request->filled('min_age') ? (int) $request->input('min_age') : null,
            maxAge: $request->filled('max_age') ? (int) $request->input('max_age') : null,
            animeId: $request->input('anime_id'),
            characterName: $request->input('character_name'),
            voicePersonId: $request->input('voice_person_id'),
            selectionId: $request->input('selection_id'),
            popular: $request->boolean('popular'),
            minAnimes: $request->filled('min_animes') ? (int) $request->input('min_animes') : null,
            recentlyAdded: $request->boolean('recently_added'),
            days: $request->filled('days') ? (int) $request->input('days') : null,
            sort: $sort,
            direction: $direction,
            perPage: $request->filled('per_page') ? (int) $request->input('per_page') : 15,
            page: $request->filled('page') ? (int) $request->input('page') : 1,
        );
    }
}
