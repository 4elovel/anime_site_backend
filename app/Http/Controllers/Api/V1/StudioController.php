<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\Studios\CreateStudio;
use AnimeSite\Actions\Studios\DeleteStudio;
use AnimeSite\Actions\Studios\GetAllStudios;
use AnimeSite\Actions\Studios\ShowStudio;
use AnimeSite\Actions\Studios\UpdateStudio;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StoreStudioRequest;
use AnimeSite\Http\Requests\UpdateStudioRequest;
use AnimeSite\Http\Resources\AnimeResource;
use AnimeSite\Http\Resources\StudioResource;
use AnimeSite\Models\Studio;

class StudioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetAllStudios $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => StudioResource::collection($paginated),
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
    public function store(StoreStudioRequest $request, CreateStudio $action): JsonResponse
    {
        $studio = $action($request->validated());

        return response()->json(
            new StudioResource($studio),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Studio $studio, ShowStudio $action): JsonResponse
    {
        $studio = $action($studio);

        return response()->json(new StudioResource($studio));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudioRequest $request, Studio $studio, UpdateStudio $action): JsonResponse
    {
        $studio = $action($studio, $request->validated());

        return response()->json(new StudioResource($studio));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Studio $studio, DeleteStudio $action): JsonResponse
    {
        $action($studio);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get animes for a studio.
     */
    public function animes(Studio $studio): JsonResponse
    {
        $animes = $studio->animes()->paginate();

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
}
