<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\Tariffs\CreateTariff;
use AnimeSite\Actions\Tariffs\DeleteTariff;
use AnimeSite\Actions\Tariffs\GetAllTariffs;
use AnimeSite\Actions\Tariffs\ShowTariff;
use AnimeSite\Actions\Tariffs\UpdateTariff;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StoreTariffRequest;
use AnimeSite\Http\Requests\UpdateTariffRequest;
use AnimeSite\Http\Resources\TariffResource;
use AnimeSite\Models\Tariff;

class TariffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetAllTariffs $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => TariffResource::collection($paginated),
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
    public function store(StoreTariffRequest $request, CreateTariff $action): JsonResponse
    {
        $tariff = $action($request->validated());
        
        return response()->json(
            new TariffResource($tariff),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Tariff $tariff, ShowTariff $action): JsonResponse
    {
        $tariff = $action($tariff);
        
        return response()->json(new TariffResource($tariff));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTariffRequest $request, Tariff $tariff, UpdateTariff $action): JsonResponse
    {
        $tariff = $action($tariff, $request->validated());
        
        return response()->json(new TariffResource($tariff));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tariff $tariff, DeleteTariff $action): JsonResponse
    {
        $action($tariff);
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
