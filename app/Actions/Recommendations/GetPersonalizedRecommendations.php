<?php

namespace AnimeSite\Actions\Recommendations;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Rating;
use AnimeSite\Models\WatchHistory;

class GetPersonalizedRecommendations
{
    /**
     * Отримати персоналізовані рекомендації аніме для авторизованого користувача.
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

        // Отримуємо ID аніме, які користувач вже оцінив або переглядав
        $watchedAnimeIds = $this->getWatchedAnimeIds($user->id);
        
        // Отримуємо жанри, які подобаються користувачу
        $favoriteGenres = $this->getFavoriteGenres($user->id);
        
        // Отримуємо студії, які подобаються користувачу
        $favoriteStudios = $this->getFavoriteStudios($user->id);

        // Будуємо запит для рекомендацій
        $query = Anime::query();
        
        // Виключаємо аніме, які користувач вже переглядав
        if (!empty($watchedAnimeIds)) {
            $query->whereNotIn('id', $watchedAnimeIds);
        }
        
        // Додаємо фільтри за жанрами, якщо є улюблені жанри
        if (!empty($favoriteGenres)) {
            $query->whereHas('genres', function ($q) use ($favoriteGenres) {
                $q->whereIn('genres.id', $favoriteGenres);
            });
        }
        
        // Додаємо фільтри за студіями, якщо є улюблені студії
        if (!empty($favoriteStudios)) {
            $query->whereIn('studio_id', $favoriteStudios);
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
        
        // Сортуємо за рейтингом та релевантністю
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
        }, fn($q) =>
            $q->orderBy('imdb_score', 'desc') // За замовчуванням - сортування за рейтингом
        );
        
        // Завантажуємо зв'язані дані
        $query->with(['studio', 'ratings', 'genres']);
        
        // Додаємо середній рейтинг
        $query->withAvg('ratings', 'number');
        
        // Повертаємо результат з пагінацією
        return $query->paginate($perPage);
    }
    
    /**
     * Отримати ID аніме, які користувач вже оцінив або переглядав.
     *
     * @param string $userId
     * @return array
     */
    private function getWatchedAnimeIds(string $userId): array
    {
        // Отримуємо ID аніме з оцінок користувача
        $ratedAnimeIds = Rating::where('user_id', $userId)
            ->pluck('anime_id')
            ->toArray();
        
        // Отримуємо ID аніме з історії переглядів користувача
        $watchedAnimeIds = WatchHistory::where('user_id', $userId)
            ->join('episodes', 'watch_histories.episode_id', '=', 'episodes.id')
            ->pluck('episodes.anime_id')
            ->toArray();
        
        // Об'єднуємо та видаляємо дублікати
        return array_unique(array_merge($ratedAnimeIds, $watchedAnimeIds));
    }
    
    /**
     * Отримати ID жанрів, які подобаються користувачу.
     *
     * @param string $userId
     * @return array
     */
    private function getFavoriteGenres(string $userId): array
    {
        // Отримуємо ID жанрів з високо оцінених аніме користувача
        return DB::table('anime_genre')
            ->join('ratings', 'anime_genre.anime_id', '=', 'ratings.anime_id')
            ->where('ratings.user_id', $userId)
            ->where('ratings.number', '>=', 7) // Високі оцінки (7+)
            ->groupBy('anime_genre.genre_id')
            ->select('anime_genre.genre_id', DB::raw('COUNT(*) as count'))
            ->orderBy('count', 'desc')
            ->limit(5) // Обмежуємо кількість жанрів
            ->pluck('genre_id')
            ->toArray();
    }
    
    /**
     * Отримати ID студій, які подобаються користувачу.
     *
     * @param string $userId
     * @return array
     */
    private function getFavoriteStudios(string $userId): array
    {
        // Отримуємо ID студій з високо оцінених аніме користувача
        return DB::table('animes')
            ->join('ratings', 'animes.id', '=', 'ratings.anime_id')
            ->where('ratings.user_id', $userId)
            ->where('ratings.number', '>=', 7) // Високі оцінки (7+)
            ->whereNotNull('animes.studio_id')
            ->groupBy('animes.studio_id')
            ->select('animes.studio_id', DB::raw('COUNT(*) as count'))
            ->orderBy('count', 'desc')
            ->limit(3) // Обмежуємо кількість студій
            ->pluck('studio_id')
            ->toArray();
    }
}
