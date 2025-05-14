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
     * Display a listing of general recommendations.
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
     * Get personalized recommendations for the authenticated user.
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
     * Get recommendations based on a specific anime.
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
     * Get recommendations based on user's watch history.
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
