<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use AnimeSite\Actions\Search\GetSearchSuggestions;
use AnimeSite\Actions\Search\PerformSearch;
use AnimeSite\Actions\Search\StoreSearchHistory;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\SearchRequest;

class SearchController extends Controller
{
    /**
     * Perform a search across the application.
     */
    public function search(SearchRequest $request, PerformSearch $action, StoreSearchHistory $historyAction): JsonResponse
    {
        $results = $action($request->validated());

        // Store search in history if user is authenticated
        if (auth()->check()) {
            $historyAction($request->validated());
        }

        return response()->json($results);
    }

    /**
     * Get search suggestions based on partial query.
     */
    public function suggestions(Request $request, GetSearchSuggestions $action): JsonResponse
    {
        $suggestions = $action($request->input('query', ''));

        return response()->json(['data' => $suggestions]);
    }
}
