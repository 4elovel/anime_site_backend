<?php

namespace AnimeSite\DTOs\Animes;

use AnimeSite\Enums\Country;
use AnimeSite\Enums\Kind;
use AnimeSite\Enums\Period;
use AnimeSite\Enums\RestrictedRating;
use AnimeSite\Enums\Source;
use AnimeSite\Enums\Status;
use AnimeSite\Enums\VideoQuality;
use Illuminate\Http\Request;

class AnimeIndexDTO
{
    /**
     * @param string|null $query Пошуковий запит
     * @param Kind[]|null $kinds Типи аніме
     * @param Status[]|null $statuses Статуси аніме
     * @param Period[]|null $periods Сезони
     * @param float|null $minScore Мінімальний рейтинг IMDB
     * @param float|null $maxScore Максимальний рейтинг IMDB
     * @param Country[]|null $countries Країни виробництва
     * @param string[]|null $studioIds ID студій
     * @param string[]|null $tagIds ID тегів
     * @param string[]|null $personIds ID людей
     * @param int|null $minYear Мінімальний рік випуску
     * @param int|null $maxYear Максимальний рік випуску
     * @param int|null $minEpisodesCount Мінімальна кількість епізодів
     * @param int|null $maxEpisodesCount Максимальна кількість епізодів
     * @param int|null $minDuration Мінімальна тривалість епізоду
     * @param int|null $maxDuration Максимальна тривалість епізоду
     * @param RestrictedRating[]|null $restrictedRatings Вікові обмеження
     * @param Source[]|null $sources Джерела матеріалу
     * @param VideoQuality[]|null $videoQualities Якість відео
     * @param bool|null $popular Фільтрувати за популярністю
     * @param int|null $minRatings Мінімальна кількість оцінок для популярних
     * @param bool|null $topRated Фільтрувати за найвищим рейтингом
     * @param float|null $minTopScore Мінімальний рейтинг для топових
     * @param bool|null $recentlyAdded Нещодавно додані
     * @param bool|null $recentlyUpdated Нещодавно оновлені
     * @param int|null $days Кількість днів для нещодавно доданих/оновлених
     * @param string|null $similarTo ID аніме для пошуку схожих
     * @param string|null $relatedTo ID аніме для пошуку пов'язаних
     * @param bool|null $isActive Фільтрувати за активністю
     * @param bool|null $isPublished Фільтрувати за публікацією
     * @param string|null $sort Поле для сортування
     * @param string|null $direction Напрямок сортування (asc/desc)
     * @param int $perPage Кількість елементів на сторінці
     * @param int $page Номер сторінки
     */
    public function __construct(
        public readonly ?string $query = null,
        public readonly ?array $kinds = null,
        public readonly ?array $statuses = null,
        public readonly ?array $periods = null,
        public readonly ?float $minScore = null,
        public readonly ?float $maxScore = null,
        public readonly ?array $countries = null,
        public readonly ?array $studioIds = null,
        public readonly ?array $tagIds = null,
        public readonly ?array $personIds = null,
        public readonly ?int $minYear = null,
        public readonly ?int $maxYear = null,
        public readonly ?int $minEpisodesCount = null,
        public readonly ?int $maxEpisodesCount = null,
        public readonly ?int $minDuration = null,
        public readonly ?int $maxDuration = null,
        public readonly ?array $restrictedRatings = null,
        public readonly ?array $sources = null,
        public readonly ?array $videoQualities = null,
        public readonly ?bool $popular = null,
        public readonly ?int $minRatings = null,
        public readonly ?bool $topRated = null,
        public readonly ?float $minTopScore = null,
        public readonly ?bool $recentlyAdded = null,
        public readonly ?bool $recentlyUpdated = null,
        public readonly ?int $days = null,
        public readonly ?string $similarTo = null,
        public readonly ?string $relatedTo = null,
        public readonly ?bool $isActive = null,
        public readonly ?bool $isPublished = null,
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
        $kinds = null;
        if ($request->filled('kinds')) {
            $kinds = array_map(
                fn($kind) => Kind::tryFrom($kind),
                is_array($request->input('kinds')) ? $request->input('kinds') : [$request->input('kinds')]
            );
            $kinds = array_filter($kinds); // Видаляємо null значення
        }

        // Обробка статусів
        $statuses = null;
        if ($request->filled('statuses')) {
            $statuses = array_map(
                fn($status) => Status::tryFrom($status),
                is_array($request->input('statuses')) ? $request->input('statuses') : [$request->input('statuses')]
            );
            $statuses = array_filter($statuses); // Видаляємо null значення
        }

        // Обробка сезонів
        $periods = null;
        if ($request->filled('periods')) {
            $periods = array_map(
                fn($period) => Period::tryFrom($period),
                is_array($request->input('periods')) ? $request->input('periods') : [$request->input('periods')]
            );
            $periods = array_filter($periods); // Видаляємо null значення
        }

        // Обробка країн
        $countries = null;
        if ($request->filled('countries')) {
            $countries = array_map(
                fn($country) => Country::tryFrom($country),
                is_array($request->input('countries')) ? $request->input('countries') : [$request->input('countries')]
            );
            $countries = array_filter($countries); // Видаляємо null значення
        }

        // Обробка вікових обмежень
        $restrictedRatings = null;
        if ($request->filled('restricted_ratings')) {
            $restrictedRatings = array_map(
                fn($rating) => RestrictedRating::tryFrom($rating),
                is_array($request->input('restricted_ratings')) ? $request->input('restricted_ratings') : [$request->input('restricted_ratings')]
            );
            $restrictedRatings = array_filter($restrictedRatings); // Видаляємо null значення
        }

        // Обробка джерел
        $sources = null;
        if ($request->filled('sources')) {
            $sources = array_map(
                fn($source) => Source::tryFrom($source),
                is_array($request->input('sources')) ? $request->input('sources') : [$request->input('sources')]
            );
            $sources = array_filter($sources); // Видаляємо null значення
        }

        // Обробка якості відео
        $videoQualities = null;
        if ($request->filled('video_qualities')) {
            $videoQualities = array_map(
                fn($quality) => VideoQuality::tryFrom($quality),
                is_array($request->input('video_qualities')) ? $request->input('video_qualities') : [$request->input('video_qualities')]
            );
            $videoQualities = array_filter($videoQualities); // Видаляємо null значення
        }

        // Обробка ID студій
        $studioIds = $request->filled('studio_ids') 
            ? (is_array($request->input('studio_ids')) ? $request->input('studio_ids') : [$request->input('studio_ids')])
            : null;

        // Обробка ID тегів
        $tagIds = $request->filled('tag_ids') 
            ? (is_array($request->input('tag_ids')) ? $request->input('tag_ids') : [$request->input('tag_ids')])
            : null;

        // Обробка ID людей
        $personIds = $request->filled('person_ids') 
            ? (is_array($request->input('person_ids')) ? $request->input('person_ids') : [$request->input('person_ids')])
            : null;

        // Обробка напрямку сортування
        $direction = $request->input('direction');
        if ($direction && !in_array(strtolower($direction), ['asc', 'desc'])) {
            $direction = 'desc'; // За замовчуванням desc, якщо передано невірне значення
        }

        // Обробка поля сортування
        $sort = $request->input('sort');
        $allowedSortFields = [
            'title', 'original_title', 'imdb_score', 'first_air_date', 
            'last_air_date', 'episodes_count', 'duration', 'created_at', 'updated_at'
        ];
        if ($sort && !in_array($sort, $allowedSortFields)) {
            $sort = 'created_at'; // За замовчуванням created_at, якщо передано невірне значення
        }

        return new self(
            query: $request->input('search'),
            kinds: $kinds,
            statuses: $statuses,
            periods: $periods,
            minScore: $request->filled('min_score') ? (float) $request->input('min_score') : null,
            maxScore: $request->filled('max_score') ? (float) $request->input('max_score') : null,
            countries: $countries,
            studioIds: $studioIds,
            tagIds: $tagIds,
            personIds: $personIds,
            minYear: $request->filled('min_year') ? (int) $request->input('min_year') : null,
            maxYear: $request->filled('max_year') ? (int) $request->input('max_year') : null,
            minEpisodesCount: $request->filled('min_episodes_count') ? (int) $request->input('min_episodes_count') : null,
            maxEpisodesCount: $request->filled('max_episodes_count') ? (int) $request->input('max_episodes_count') : null,
            minDuration: $request->filled('min_duration') ? (int) $request->input('min_duration') : null,
            maxDuration: $request->filled('max_duration') ? (int) $request->input('max_duration') : null,
            restrictedRatings: $restrictedRatings,
            sources: $sources,
            videoQualities: $videoQualities,
            popular: $request->boolean('popular'),
            minRatings: $request->filled('min_ratings') ? (int) $request->input('min_ratings') : null,
            topRated: $request->boolean('top_rated'),
            minTopScore: $request->filled('min_top_score') ? (float) $request->input('min_top_score') : null,
            recentlyAdded: $request->boolean('recently_added'),
            recentlyUpdated: $request->boolean('recently_updated'),
            days: $request->filled('days') ? (int) $request->input('days') : null,
            similarTo: $request->input('similar_to'),
            relatedTo: $request->input('related_to'),
            isActive: $request->filled('is_active') ? filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN) : null,
            isPublished: $request->filled('is_published') ? filter_var($request->input('is_published'), FILTER_VALIDATE_BOOLEAN) : null,
            sort: $sort,
            direction: $direction,
            perPage: $request->filled('per_page') ? (int) $request->input('per_page') : 15,
            page: $request->filled('page') ? (int) $request->input('page') : 1,
        );
    }
}
