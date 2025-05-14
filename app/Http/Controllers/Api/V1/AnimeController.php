<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\Animes\CreateAnime;
use AnimeSite\Actions\Animes\DeleteAnime;
use AnimeSite\Actions\Animes\GetAllAnimes;
use AnimeSite\Actions\Animes\ShowAnime;
use AnimeSite\Actions\Animes\UpdateAnime;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StoreAnimeRequest;
use AnimeSite\Http\Requests\UpdateAnimeRequest;
use AnimeSite\Http\Resources\AnimeResource;
use AnimeSite\Http\Resources\CommentResource;
use AnimeSite\Http\Resources\EpisodeResource;
use AnimeSite\Http\Resources\RatingResource;
use AnimeSite\Models\Anime;

class AnimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetAllAnimes $action): JsonResponse
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
     * Store a newly created resource in storage.
     */
    public function store(StoreAnimeRequest $request, CreateAnime $action): JsonResponse
    {
        $anime = $action($request->validated());

        return response()->json(
            new AnimeResource($anime),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Anime $anime, ShowAnime $action): JsonResponse
    {
        $anime = $action($anime);

        return response()->json(new AnimeResource($anime));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnimeRequest $request, Anime $anime, UpdateAnime $action): JsonResponse
    {
        $anime = $action($anime, $request->validated());

        return response()->json(new AnimeResource($anime));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anime $anime, DeleteAnime $action): JsonResponse
    {
        $action($anime);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get trending anime.
     */
    public function trending(Request $request, GetAllAnimes $action): JsonResponse
    {
        $request->merge(['sort' => 'trending']);
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
     * Get popular anime.
     */
    public function popular(Request $request, GetAllAnimes $action): JsonResponse
    {
        $request->merge(['sort' => 'popular']);
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
     * Get current season anime.
     */
    public function currentSeason(Request $request, GetAllAnimes $action): JsonResponse
    {
        $request->merge(['period' => 'current_season']);
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
     * Get upcoming anime.
     */
    public function upcoming(Request $request, GetAllAnimes $action): JsonResponse
    {
        $request->merge(['period' => 'upcoming']);
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
     * Get episodes for an anime.
     */
    public function episodes(Anime $anime): JsonResponse
    {
        $episodes = $anime->episodes()->paginate();

        return response()->json([
            'data' => EpisodeResource::collection($episodes),
            'meta' => [
                'current_page' => $episodes->currentPage(),
                'last_page' => $episodes->lastPage(),
                'per_page' => $episodes->perPage(),
                'total' => $episodes->total(),
            ],
        ]);
    }

    /**
     * Get ratings for an anime.
     */
    public function ratings(Anime $anime): JsonResponse
    {
        $ratings = $anime->ratings()->paginate();

        return response()->json([
            'data' => RatingResource::collection($ratings),
            'meta' => [
                'current_page' => $ratings->currentPage(),
                'last_page' => $ratings->lastPage(),
                'per_page' => $ratings->perPage(),
                'total' => $ratings->total(),
            ],
        ]);
    }

    /**
     * Get comments for an anime.
     */
    public function comments(Anime $anime): JsonResponse
    {
        $comments = $anime->comments()->paginate();

        return response()->json([
            'data' => CommentResource::collection($comments),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    /**
     * Get similar anime.
     */
    public function similar(Anime $anime): JsonResponse
    {
        $similar = $anime->getSimilarAnime()->paginate();

        return response()->json([
            'data' => AnimeResource::collection($similar),
            'meta' => [
                'current_page' => $similar->currentPage(),
                'last_page' => $similar->lastPage(),
                'per_page' => $similar->perPage(),
                'total' => $similar->total(),
            ],
        ]);
    }

    /**
     * Get related anime.
     */
    public function related(Anime $anime): JsonResponse
    {
        $related = $anime->getRelatedAnime()->paginate();

        return response()->json([
            'data' => AnimeResource::collection($related),
            'meta' => [
                'current_page' => $related->currentPage(),
                'last_page' => $related->lastPage(),
                'per_page' => $related->perPage(),
                'total' => $related->total(),
            ],
        ]);
    }
}
