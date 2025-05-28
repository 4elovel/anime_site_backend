<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\Tariffs\CompareTariffs;
use AnimeSite\Actions\Tariffs\CreateTariff;
use AnimeSite\Actions\Tariffs\DeleteTariff;
use AnimeSite\Actions\Tariffs\GetActiveTariffs;
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
     * Отримати список тарифів.
     *
     * @param Request $request
     * @param GetAllTariffs $action
     * @return JsonResponse
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
     * Отримати список активних тарифів.
     *
     * @param Request $request
     * @param GetActiveTariffs $action
     * @return JsonResponse
     */
    public function active(Request $request, GetActiveTariffs $action): JsonResponse
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
     * Створити новий тариф.
     *
     * @param StoreTariffRequest $request
     * @param CreateTariff $action
     * @return JsonResponse
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
     * Отримати інформацію про конкретний тариф.
     *
     * @param Tariff $tariff
     * @param ShowTariff $action
     * @return JsonResponse
     */
    public function show(Tariff $tariff, ShowTariff $action): JsonResponse
    {
        $tariff = $action($tariff);

        return response()->json(new TariffResource($tariff));
    }

    /**
     * Оновити тариф.
     *
     * @param UpdateTariffRequest $request
     * @param Tariff $tariff
     * @param UpdateTariff $action
     * @return JsonResponse
     */
    public function update(UpdateTariffRequest $request, Tariff $tariff, UpdateTariff $action): JsonResponse
    {
        $tariff = $action($tariff, $request->validated());

        return response()->json(new TariffResource($tariff));
    }

    /**
     * Видалити тариф.
     *
     * @param Tariff $tariff
     * @param DeleteTariff $action
     * @return JsonResponse
     */
    public function destroy(Tariff $tariff, DeleteTariff $action): JsonResponse
    {
        $action($tariff);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Порівняти тарифи.
     *
     * @param Request $request
     * @param CompareTariffs $action
     * @return JsonResponse
     */
    public function compare(Request $request, CompareTariffs $action): JsonResponse
    {
        $tariffIds = $request->validate([
            'tariff_ids' => 'required|array|min:2',
            'tariff_ids.*' => 'required|ulid|exists:tariffs,id',
        ])['tariff_ids'];

        $comparison = $action($tariffIds);

        return response()->json([
            'data' => $comparison,
        ]);
    }
}
