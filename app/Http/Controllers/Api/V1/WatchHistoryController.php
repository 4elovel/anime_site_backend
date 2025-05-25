<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\WatchHistories\CleanOldWatchHistory;
use AnimeSite\Actions\WatchHistories\ClearUserWatchHistory;
use AnimeSite\Actions\WatchHistories\ClearWatchHistory;
use AnimeSite\Actions\WatchHistories\CreateWatchHistory;
use AnimeSite\Actions\WatchHistories\DeleteWatchHistory;
use AnimeSite\Actions\WatchHistories\GetAllWatchHistories;
use AnimeSite\Actions\WatchHistories\GetUserWatchHistory;
use AnimeSite\Actions\WatchHistories\ShowWatchHistory;
use AnimeSite\Actions\WatchHistories\UpdateWatchHistory;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StoreWatchHistoryRequest;
use AnimeSite\Http\Requests\UpdateWatchHistoryRequest;
use AnimeSite\Http\Resources\WatchHistoryResource;
use AnimeSite\Models\User;
use AnimeSite\Models\WatchHistory;

class WatchHistoryController extends Controller
{
    /**
     * Отримати список історії переглядів.
     *
     * @param Request $request
     * @param GetAllWatchHistories $action
     * @return JsonResponse
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
     * Створити новий запис історії переглядів.
     *
     * @param StoreWatchHistoryRequest $request
     * @param CreateWatchHistory $action
     * @return JsonResponse
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
     * Отримати інформацію про конкретний запис історії переглядів.
     *
     * @param WatchHistory $watchHistory
     * @param ShowWatchHistory $action
     * @return JsonResponse
     */
    public function show(WatchHistory $watchHistory, ShowWatchHistory $action): JsonResponse
    {
        $watchHistory = $action($watchHistory);

        return response()->json(new WatchHistoryResource($watchHistory));
    }

    /**
     * Оновити запис історії переглядів.
     *
     * @param UpdateWatchHistoryRequest $request
     * @param WatchHistory $watchHistory
     * @param UpdateWatchHistory $action
     * @return JsonResponse
     */
    public function update(UpdateWatchHistoryRequest $request, WatchHistory $watchHistory, UpdateWatchHistory $action): JsonResponse
    {
        $watchHistory = $action($watchHistory, $request->validated());

        return response()->json(new WatchHistoryResource($watchHistory));
    }

    /**
     * Видалити запис історії переглядів.
     *
     * @param WatchHistory $watchHistory
     * @param DeleteWatchHistory $action
     * @return JsonResponse
     */
    public function destroy(WatchHistory $watchHistory, DeleteWatchHistory $action): JsonResponse
    {
        $action($watchHistory);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Очистити всю історію переглядів для авторизованого користувача.
     *
     * @param ClearWatchHistory $action
     * @return JsonResponse
     */
    public function clear(ClearWatchHistory $action): JsonResponse
    {
        $action();

        return response()->json(['message' => 'Історія переглядів успішно очищена']);
    }

    /**
     * Очистити стару історію переглядів для авторизованого користувача.
     *
     * @param Request $request
     * @param CleanOldWatchHistory $action
     * @return JsonResponse
     */
    public function cleanOld(Request $request, CleanOldWatchHistory $action): JsonResponse
    {
        $days = (int) $request->input('days', 30);
        $action(auth()->id(), $days);

        return response()->json(['message' => 'Стара історія переглядів успішно очищена']);
    }

    /**
     * Отримати історію переглядів конкретного користувача.
     *
     * @param User $user
     * @param Request $request
     * @param GetUserWatchHistory $action
     * @return JsonResponse
     */
    public function userWatchHistory(User $user, Request $request, GetUserWatchHistory $action): JsonResponse
    {
        $paginated = $action($user, $request);

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
     * Очистити всю історію переглядів конкретного користувача.
     *
     * @param User $user
     * @param ClearUserWatchHistory $action
     * @return JsonResponse
     */
    public function clearUserHistory(User $user, ClearUserWatchHistory $action): JsonResponse
    {
        $action($user);

        return response()->json(['message' => 'Історія переглядів користувача успішно очищена']);
    }
}
