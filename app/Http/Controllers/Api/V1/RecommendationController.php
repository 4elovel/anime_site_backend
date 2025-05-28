<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use AnimeSite\Actions\Recommendations\GetAllRecommendations;
use AnimeSite\Actions\Recommendations\GetBasedOnAnimeRecommendations;
use AnimeSite\Actions\Recommendations\GetBasedOnHistoryRecommendations;
use AnimeSite\Actions\Recommendations\GetPersonalizedRecommendations;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Resources\AnimeResource;
use AnimeSite\Models\Anime;

class RecommendationController extends Controller
{
    /**
     * Отримати список загальних рекомендацій.
     *
     * @param Request $request
     * @param GetAllRecommendations $action
     * @return JsonResponse
     */
    public function index(Request $request, GetAllRecommendations $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => AnimeResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Отримати персоналізовані рекомендації для авторизованого користувача.
     *
     * @param Request $request
     * @param GetPersonalizedRecommendations $action
     * @return JsonResponse
     */
    public function personalized(Request $request, GetPersonalizedRecommendations $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => AnimeResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Отримати рекомендації на основі конкретного аніме.
     *
     * @param Anime $anime
     * @param Request $request
     * @param GetBasedOnAnimeRecommendations $action
     * @return JsonResponse
     */
    public function basedOnAnime(Anime $anime, Request $request, GetBasedOnAnimeRecommendations $action): JsonResponse
    {
        $paginated = $action($anime, $request);

        return response()->json([
            'data' => AnimeResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Отримати рекомендації на основі історії переглядів користувача.
     *
     * @param Request $request
     * @param GetBasedOnHistoryRecommendations $action
     * @return JsonResponse
     */
    public function basedOnHistory(Request $request, GetBasedOnHistoryRecommendations $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => AnimeResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }
}
