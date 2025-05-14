<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\WatchHistories\ClearWatchHistory;
use AnimeSite\Actions\WatchHistories\CreateWatchHistory;
use AnimeSite\Actions\WatchHistories\DeleteWatchHistory;
use AnimeSite\Actions\WatchHistories\GetAllWatchHistories;
use AnimeSite\Actions\WatchHistories\ShowWatchHistory;
use AnimeSite\Actions\WatchHistories\UpdateWatchHistory;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StoreWatchHistoryRequest;
use AnimeSite\Http\Requests\UpdateWatchHistoryRequest;
use AnimeSite\Http\Resources\WatchHistoryResource;
use AnimeSite\Models\WatchHistory;

class WatchHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetAllWatchHistories $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => WatchHistoryResource::collection($paginated),
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
    public function store(StoreWatchHistoryRequest $request, CreateWatchHistory $action): JsonResponse
    {
        $watchHistory = $action($request->validated());

        return response()->json(
            new WatchHistoryResource($watchHistory),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(WatchHistory $watchHistory, ShowWatchHistory $action): JsonResponse
    {
        $watchHistory = $action($watchHistory);

        return response()->json(new WatchHistoryResource($watchHistory));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWatchHistoryRequest $request, WatchHistory $watchHistory, UpdateWatchHistory $action): JsonResponse
    {
        $watchHistory = $action($watchHistory, $request->validated());

        return response()->json(new WatchHistoryResource($watchHistory));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WatchHistory $watchHistory, DeleteWatchHistory $action): JsonResponse
    {
        $action($watchHistory);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Clear all watch history for the authenticated user.
     */
    public function clear(ClearWatchHistory $action): JsonResponse
    {
        $action();

        return response()->json(['message' => 'Watch history cleared successfully']);
    }
}
