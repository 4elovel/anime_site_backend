<?php

namespace AnimeSite\Actions\Recommendations;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use AnimeSite\Models\Anime;

class GetBasedOnAnimeRecommendations
{
    /**
     * Отримати рекомендації на основі конкретного аніме.
     *
     * @param Anime $anime
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Anime $anime, Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->input('per_page', 15);
        
        // Отримуємо ID жанрів поточного аніме
        $genreIds = $anime->genres()->pluck('genres.id')->toArray();
        
        // Отримуємо ID студії поточного аніме
        $studioId = $anime->studio_id;
        
        // Будуємо запит для рекомендацій
        $query = Anime::query();
        
        // Виключаємо поточне аніме
        $query->where('id', '!=', $anime->id);
        
        // Додаємо фільтр за жанрами
        if (!empty($genreIds)) {
            $query->whereHas('genres', function ($q) use ($genreIds) {
                $q->whereIn('genres.id', $genreIds);
            });
        }
        
        // Додаємо фільтр за студією
        if ($studioId) {
            $query->where(function ($q) use ($studioId) {
                $q->where('studio_id', $studioId)
                  ->orWhereHas('genres', function ($subQ) use ($studioId) {
                      $subQ->whereIn('genres.id', function ($genreQ) use ($studioId) {
                          $genreQ->select('anime_genre.genre_id')
                                ->from('animes')
                                ->join('anime_genre', 'animes.id', '=', 'anime_genre.anime_id')
                                ->where('animes.studio_id', $studioId);
                      });
                  });
            });
        }
        
        // Додаємо фільтр за типом аніме
        $query->when($anime->kind, fn($q) =>
            $q->ofKind($anime->kind)
        );
        
        // Додаємо фільтри з запиту
        $query->when($request->filled('status'), fn($q) =>
            $q->withStatus($request->input('status'))
        , fn($q) =>
            $q->withStatus('released') // За замовчуванням - released
        );
        
        $query->when($request->filled('period'), fn($q) =>
            $q->ofPeriod($request->input('period'))
        );
        
        // Сортуємо за релевантністю (кількістю спільних жанрів)
        $query->when($request->filled('sort'), function ($q) use ($request) {
            $sort = $request->input('sort');
            $direction = 'asc';
            
            if (str_starts_with($sort, '-')) {
                $direction = 'desc';
                $sort = substr($sort, 1);
            }
            
            if (in_array($sort, ['imdb_score', 'created_at', 'first_air_date'])) {
                $q->orderBy($sort, $direction);
            }
        }, function ($q) use ($genreIds) {
            // За замовчуванням - сортування за кількістю спільних жанрів
            if (!empty($genreIds)) {
                $q->withCount(['genres' => function ($subQ) use ($genreIds) {
                    $subQ->whereIn('genres.id', $genreIds);
                }])->orderBy('genres_count', 'desc');
            } else {
                $q->orderBy('imdb_score', 'desc');
            }
        });
        
        // Завантажуємо зв'язані дані
        $query->with(['studio', 'ratings', 'genres']);
        
        // Додаємо середній рейтинг
        $query->withAvg('ratings', 'number');
        
        // Повертаємо результат з пагінацією
        return $query->paginate($perPage);
    }
}
