<?php

namespace AnimeSite\Actions\Recommendations;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use AnimeSite\Models\Anime;
use AnimeSite\Models\WatchHistory;

class GetBasedOnHistoryRecommendations
{
    /**
     * Отримати рекомендації на основі історії переглядів користувача.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        // Перевіряємо, чи користувач авторизований
        if (!Auth::check()) {
            // Якщо користувач не авторизований, повертаємо загальні рекомендації
            $getAllRecommendations = app(GetAllRecommendations::class);
            return $getAllRecommendations($request);
        }

        $user = Auth::user();
        $perPage = (int) $request->input('per_page', 15);

        // Отримуємо ID аніме з історії переглядів користувача
        $watchedAnimeIds = $this->getWatchedAnimeIds($user->id);
        
        // Якщо історія переглядів порожня, повертаємо загальні рекомендації
        if (empty($watchedAnimeIds)) {
            $getAllRecommendations = app(GetAllRecommendations::class);
            return $getAllRecommendations($request);
        }
        
        // Отримуємо ID жанрів з переглянутих аніме
        $genreIds = $this->getGenresFromWatchedAnime($watchedAnimeIds);
        
        // Будуємо запит для рекомендацій
        $query = Anime::query();
        
        // Виключаємо аніме, які користувач вже переглядав
        $query->whereNotIn('id', $watchedAnimeIds);
        
        // Додаємо фільтр за жанрами
        if (!empty($genreIds)) {
            $query->whereHas('genres', function ($q) use ($genreIds) {
                $q->whereIn('genres.id', $genreIds);
            });
        }
        
        // Додаємо фільтри з запиту
        $query->when($request->filled('status'), fn($q) =>
            $q->withStatus($request->input('status'))
        , fn($q) =>
            $q->withStatus('released') // За замовчуванням - released
        );
        
        $query->when($request->filled('kind'), fn($q) =>
            $q->ofKind($request->input('kind'))
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
    
    /**
     * Отримати ID аніме з історії переглядів користувача.
     *
     * @param string $userId
     * @return array
     */
    private function getWatchedAnimeIds(string $userId): array
    {
        return WatchHistory::where('user_id', $userId)
            ->join('episodes', 'watch_histories.episode_id', '=', 'episodes.id')
            ->pluck('episodes.anime_id')
            ->unique()
            ->values()
            ->toArray();
    }
    
    /**
     * Отримати ID жанрів з переглянутих аніме.
     *
     * @param array $animeIds
     * @return array
     */
    private function getGenresFromWatchedAnime(array $animeIds): array
    {
        return DB::table('anime_genre')
            ->whereIn('anime_id', $animeIds)
            ->groupBy('genre_id')
            ->select('genre_id', DB::raw('COUNT(*) as count'))
            ->orderBy('count', 'desc')
            ->limit(10) // Обмежуємо кількість жанрів
            ->pluck('genre_id')
            ->toArray();
    }
}
