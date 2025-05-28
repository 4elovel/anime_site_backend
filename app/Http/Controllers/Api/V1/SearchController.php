<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use AnimeSite\Actions\Search\ClearUserSearchHistory;
use AnimeSite\Actions\Search\GetSearchSuggestions;
use AnimeSite\Actions\Search\GetUserSearchHistory;
use AnimeSite\Actions\Search\PerformSearch;
use AnimeSite\Actions\Search\StoreSearchHistory;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\SearchRequest;
use AnimeSite\Http\Resources\SearchHistoryResource;

class SearchController extends Controller
{
    /**
     * Виконати пошук по всіх моделях додатку.
     *
     * @param SearchRequest $request
     * @param PerformSearch $action
     * @param StoreSearchHistory $historyAction
     * @return JsonResponse
     */
    public function search(SearchRequest $request, PerformSearch $action, StoreSearchHistory $historyAction): JsonResponse
    {
        $results = $action($request->validated());

        // Зберігаємо пошуковий запит в історію, якщо користувач авторизований
        if (auth()->check()) {
            $historyAction($request->validated());
        }

        return response()->json($results);
    }

    /**
     * Отримати пошукові підказки на основі часткового запиту.
     *
     * @param Request $request
     * @param GetSearchSuggestions $action
     * @return JsonResponse
     */
    public function suggestions(Request $request, GetSearchSuggestions $action): JsonResponse
    {
        $suggestions = $action($request->input('query', ''));

        return response()->json(['data' => $suggestions]);
    }

    /**
     * Отримати історію пошуку поточного користувача.
     *
     * @param Request $request
     * @param GetUserSearchHistory $action
     * @return JsonResponse
     */
    public function history(Request $request, GetUserSearchHistory $action): JsonResponse
    {
        // Перевіряємо, чи користувач авторизований
        if (!Auth::check()) {
            return response()->json(['message' => 'Для перегляду історії пошуку необхідно авторизуватись'], Response::HTTP_UNAUTHORIZED);
        }

        $paginated = $action(Auth::user(), $request);

        return response()->json([
            'data' => SearchHistoryResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Очистити історію пошуку поточного користувача.
     *
     * @param ClearUserSearchHistory $action
     * @return JsonResponse
     */
    public function clearHistory(ClearUserSearchHistory $action): JsonResponse
    {
        // Перевіряємо, чи користувач авторизований
        if (!Auth::check()) {
            return response()->json(['message' => 'Для очищення історії пошуку необхідно авторизуватись'], Response::HTTP_UNAUTHORIZED);
        }

        $action(Auth::user());

        return response()->json(['message' => 'Історія пошуку успішно очищена']);
    }
}
