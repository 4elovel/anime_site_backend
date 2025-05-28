<?php

namespace AnimeSite\Http\Controllers\Api;

use AnimeSite\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use AnimeSite\Models\Anime;

class TestController extends Controller
{
    /**
     * Отримати список аніме без авторизації
     *
     * @return JsonResponse
     */
    public function animes(): JsonResponse
    {
        $animes = Anime::with(['studio', 'tags', 'ratings'])
            ->withAvg('ratings', 'number')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'data' => $animes->items(),
            'meta' => [
                'current_page' => $animes->currentPage(),
                'last_page' => $animes->lastPage(),
                'per_page' => $animes->perPage(),
                'total' => $animes->total(),
            ],
        ]);
    }
}
