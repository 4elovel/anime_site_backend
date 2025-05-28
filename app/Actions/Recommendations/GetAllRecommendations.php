<?php

namespace AnimeSite\Actions\Recommendations;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Anime;

class GetAllRecommendations
{
    /**
     * Отримати загальні рекомендації аніме з пагінацією та фільтрацією.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        // Загальні рекомендації доступні всім користувачам
        $perPage = (int) $request->input('per_page', 15);

        // Отримуємо аніме з високим рейтингом та популярністю
        return Anime::query()
            // Фільтруємо за статусом (за замовчуванням - released)
            ->when($request->filled('status'), fn($q) =>
                $q->withStatus($request->input('status'))
            , fn($q) =>
                $q->withStatus('released')
            )
            // Фільтруємо за типом
            ->when($request->filled('kind'), fn($q) =>
                $q->ofKind($request->input('kind'))
            )
            // Фільтруємо за сезоном
            ->when($request->filled('period'), fn($q) =>
                $q->ofPeriod($request->input('period'))
            )
            // Фільтруємо за країною
            ->when($request->filled('country'), fn($q) =>
                $q->fromCountry($request->input('country'))
            )
            // Фільтруємо за рейтингом IMDB
            ->when($request->filled('min_imdb_score'), fn($q) =>
                $q->withImdbScoreGreaterThan($request->input('min_imdb_score'))
            , fn($q) =>
                $q->withImdbScoreGreaterThan(7.0) // За замовчуванням - рейтинг вище 7.0
            )
            // Фільтруємо за роком випуску
            ->when($request->filled('year'), fn($q) =>
                $q->whereYear('first_air_date', $request->input('year'))
            )
            // Сортуємо за рейтингом та популярністю
            ->when($request->filled('sort'), function ($query) use ($request) {
                $sort = $request->input('sort');
                $direction = 'asc';
                
                if (str_starts_with($sort, '-')) {
                    $direction = 'desc';
                    $sort = substr($sort, 1);
                }
                
                if (in_array($sort, ['imdb_score', 'created_at', 'first_air_date'])) {
                    $query->orderBy($sort, $direction);
                }
            }, fn($q) =>
                $q->orderBy('imdb_score', 'desc') // За замовчуванням - сортування за рейтингом
            )
            // Завантажуємо зв'язані дані
            ->with(['studio', 'ratings'])
            // Додаємо середній рейтинг
            ->withAvg('ratings', 'number')
            // Пагінація
            ->paginate($perPage);
    }
}
