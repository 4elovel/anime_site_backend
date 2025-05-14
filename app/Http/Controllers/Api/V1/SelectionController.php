<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\Selections\AddItemsToSelection;
use AnimeSite\Actions\Selections\CreateSelection;
use AnimeSite\Actions\Selections\DeleteSelection;
use AnimeSite\Actions\Selections\GetAllSelections;
use AnimeSite\Actions\Selections\GetFeaturedSelections;
use AnimeSite\Actions\Selections\RemoveItemsFromSelection;
use AnimeSite\Actions\Selections\ShowSelection;
use AnimeSite\Actions\Selections\UpdateSelection;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\AddSelectionItemsRequest;
use AnimeSite\Http\Requests\RemoveSelectionItemsRequest;
use AnimeSite\Http\Requests\StoreSelectionRequest;
use AnimeSite\Http\Requests\UpdateSelectionRequest;
use AnimeSite\Http\Resources\SelectionResource;
use AnimeSite\Models\Selection;

class SelectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetAllSelections $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => SelectionResource::collection($paginated),
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
    public function store(StoreSelectionRequest $request, CreateSelection $action): JsonResponse
    {
        $selection = $action($request->validated());

        return response()->json(
            new SelectionResource($selection),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Selection $selection, ShowSelection $action): JsonResponse
    {
        $selection = $action($selection);

        return response()->json(new SelectionResource($selection));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSelectionRequest $request, Selection $selection, UpdateSelection $action): JsonResponse
    {
        $selection = $action($selection, $request->validated());

        return response()->json(new SelectionResource($selection));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Selection $selection, DeleteSelection $action): JsonResponse
    {
        $action($selection);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get featured selections.
     */
    public function featured(Request $request, GetFeaturedSelections $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => SelectionResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Add items to a selection.
     */
    public function addItems(AddSelectionItemsRequest $request, Selection $selection, AddItemsToSelection $action): JsonResponse
    {
        $selection = $action($selection, $request->validated());

        return response()->json(new SelectionResource($selection));
    }

    /**
     * Remove items from a selection.
     */
    public function removeItems(RemoveSelectionItemsRequest $request, Selection $selection, RemoveItemsFromSelection $action): JsonResponse
    {
        $selection = $action($selection, $request->validated());

        return response()->json(new SelectionResource($selection));
    }
}
