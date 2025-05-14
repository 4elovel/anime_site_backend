<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\Ratings\CreateRating;
use AnimeSite\Actions\Ratings\DeleteRating;
use AnimeSite\Actions\Ratings\GetAllRatings;
use AnimeSite\Actions\Ratings\ShowRating;
use AnimeSite\Actions\Ratings\UpdateRating;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StoreRatingRequest;
use AnimeSite\Http\Requests\UpdateRatingRequest;
use AnimeSite\Http\Resources\RatingResource;
use AnimeSite\Models\Rating;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetAllRatings $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => RatingResource::collection($paginated),
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
    public function store(StoreRatingRequest $request, CreateRating $action): JsonResponse
    {
        $rating = $action($request->validated());

        return response()->json(
            new RatingResource($rating),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Rating $rating, ShowRating $action): JsonResponse
    {
        $rating = $action($rating);

        return response()->json(new RatingResource($rating));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRatingRequest $request, Rating $rating, UpdateRating $action): JsonResponse
    {
        $rating = $action($rating, $request->validated());

        return response()->json(new RatingResource($rating));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rating $rating, DeleteRating $action): JsonResponse
    {
        $action($rating);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
