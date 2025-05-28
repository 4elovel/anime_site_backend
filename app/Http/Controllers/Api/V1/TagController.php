<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\Tags\CreateTag;
use AnimeSite\Actions\Tags\DeleteTag;
use AnimeSite\Actions\Tags\GetAllTags;
use AnimeSite\Actions\Tags\ShowTag;
use AnimeSite\Actions\Tags\UpdateTag;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StoreTagRequest;
use AnimeSite\Http\Requests\UpdateTagRequest;
use AnimeSite\Http\Resources\AnimeResource;
use AnimeSite\Http\Resources\TagResource;
use AnimeSite\Models\Tag;

class TagController extends Controller
{
    public function index(Request $request, GetAllTags $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => TagResource::collection($paginated),
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
    public function store(StoreTagRequest $request, CreateTag $action): JsonResponse
    {
        $tag = $action($request->validated());

        return response()->json(
            new TagResource($tag),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag, ShowTag $action): JsonResponse
    {
        $tag = $action($tag);

        return response()->json(new TagResource($tag));
    }

    /**
     * Get animes related to the tag.
     */
    public function animes(Tag $tag): JsonResponse
    {
        $animes = $tag->animes()->paginate();

        return response()->json([
            'data' => AnimeResource::collection($animes),
            'meta' => [
                'current_page' => $animes->currentPage(),
                'last_page' => $animes->lastPage(),
                'per_page' => $animes->perPage(),
                'total' => $animes->total(),
            ],
        ]);
    }

    public function update(UpdateTagRequest $request, Tag $tag, UpdateTag $action): JsonResponse
    {
        $tag = $action($tag, $request->validated());
        return response()->json(new TagResource($tag));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag, DeleteTag $action): JsonResponse
    {
        $action($tag);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get all genres (tags with is_genre=true).
     */
    public function genres(Request $request, GetAllTags $action): JsonResponse
    {
        $request->merge(['is_genre' => true]);
        $paginated = $action($request);

        return response()->json([
            'data' => TagResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }
}
