<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use AnimeSite\Actions\Achievements\GetAllAchievements;
use AnimeSite\Actions\Achievements\GetUserAchievements;
use AnimeSite\Actions\Achievements\ShowAchievement;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Resources\AchievementResource;
use AnimeSite\Models\Achievement;
use AnimeSite\Models\User;

class AchievementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetAllAchievements $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => AchievementResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Achievement $achievement, ShowAchievement $action): JsonResponse
    {
        $achievement = $action($achievement);

        return response()->json(new AchievementResource($achievement));
    }

    /**
     * Get achievements for a specific user.
     */
    public function userAchievements(User $user, Request $request, GetUserAchievements $action): JsonResponse
    {
        $paginated = $action($user, $request);

        return response()->json([
            'data' => AchievementResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }
}
