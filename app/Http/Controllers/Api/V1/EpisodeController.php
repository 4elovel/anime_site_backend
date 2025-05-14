<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\Episodes\CreateEpisode;
use AnimeSite\Actions\Episodes\DeleteEpisode;
use AnimeSite\Actions\Episodes\GetAllEpisodes;
use AnimeSite\Actions\Episodes\ShowEpisode;
use AnimeSite\Actions\Episodes\UpdateEpisode;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StoreEpisodeRequest;
use AnimeSite\Http\Requests\UpdateEpisodeRequest;
use AnimeSite\Http\Resources\CommentResource;
use AnimeSite\Http\Resources\EpisodeResource;
use AnimeSite\Models\Episode;

class EpisodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetAllEpisodes $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => EpisodeResource::collection($paginated),
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
    public function store(StoreEpisodeRequest $request, CreateEpisode $action): JsonResponse
    {
        $episode = $action($request->validated());

        return response()->json(
            new EpisodeResource($episode),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Episode $episode, ShowEpisode $action): JsonResponse
    {
        $episode = $action($episode);

        return response()->json(new EpisodeResource($episode));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEpisodeRequest $request, Episode $episode, UpdateEpisode $action): JsonResponse
    {
        $episode = $action($episode, $request->validated());

        return response()->json(new EpisodeResource($episode));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Episode $episode, DeleteEpisode $action): JsonResponse
    {
        $action($episode);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get comments for an episode.
     */
    public function comments(Episode $episode): JsonResponse
    {
        $comments = $episode->comments()->paginate();

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
     * Get latest episodes.
     */
    public function latest(Request $request, GetAllEpisodes $action): JsonResponse
    {
        $request->merge(['sort' => 'latest']);
        $paginated = $action($request);

        return response()->json([
            'data' => EpisodeResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }
}
