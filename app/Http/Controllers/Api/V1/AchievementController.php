<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\Achievements\CreateAchievement;
use AnimeSite\Actions\Achievements\DeleteAchievement;
use AnimeSite\Actions\Achievements\GetAllAchievements;
use AnimeSite\Actions\Achievements\GetUserAchievements;
use AnimeSite\Actions\Achievements\ShowAchievement;
use AnimeSite\Actions\Achievements\UpdateAchievement;
use AnimeSite\Actions\AchievementUsers\CreateAchievementUser;
use AnimeSite\Actions\AchievementUsers\DeleteAchievementUser;
use AnimeSite\Actions\AchievementUsers\GetAllAchievementUsers;
use AnimeSite\Actions\AchievementUsers\ShowAchievementUser;
use AnimeSite\Actions\AchievementUsers\UpdateAchievementUser;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StoreAchievementRequest;
use AnimeSite\Http\Requests\StoreAchievementUserRequest;
use AnimeSite\Http\Requests\UpdateAchievementRequest;
use AnimeSite\Http\Requests\UpdateAchievementUserRequest;
use AnimeSite\Http\Resources\AchievementResource;
use AnimeSite\Http\Resources\AchievementUserResource;
use AnimeSite\Models\Achievement;
use AnimeSite\Models\AchievementUser;
use AnimeSite\Models\User;

class AchievementController extends Controller
{
    /**
     * Display a listing of the achievements.
     *
     * @param Request $request
     * @param GetAllAchievements $action
     * @return JsonResponse
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
     * Store a newly created achievement in storage.
     *
     * @param StoreAchievementRequest $request
     * @param CreateAchievement $action
     * @return JsonResponse
     */
    public function store(StoreAchievementRequest $request, CreateAchievement $action): JsonResponse
    {
        $achievement = $action($request->validated());

        return response()->json(
            new AchievementResource($achievement),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified achievement.
     *
     * @param Achievement $achievement
     * @param ShowAchievement $action
     * @return JsonResponse
     */
    public function show(Achievement $achievement, ShowAchievement $action): JsonResponse
    {
        $achievement = $action($achievement);

        return response()->json(new AchievementResource($achievement));
    }

    /**
     * Update the specified achievement in storage.
     *
     * @param UpdateAchievementRequest $request
     * @param Achievement $achievement
     * @param UpdateAchievement $action
     * @return JsonResponse
     */
    public function update(UpdateAchievementRequest $request, Achievement $achievement, UpdateAchievement $action): JsonResponse
    {
        $achievement = $action($achievement, $request->validated());

        return response()->json(new AchievementResource($achievement));
    }

    /**
     * Remove the specified achievement from storage.
     *
     * @param Achievement $achievement
     * @param DeleteAchievement $action
     * @return JsonResponse
     */
    public function destroy(Achievement $achievement, DeleteAchievement $action): JsonResponse
    {
        $action($achievement);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get achievements for a specific user.
     *
     * @param User $user
     * @param Request $request
     * @param GetUserAchievements $action
     * @return JsonResponse
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

    /**
     * Display a listing of achievement users.
     *
     * @param Request $request
     * @param GetAllAchievementUsers $action
     * @return JsonResponse
     */
    public function achievementUsers(Request $request, GetAllAchievementUsers $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => AchievementUserResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Store a newly created achievement user in storage.
     *
     * @param StoreAchievementUserRequest $request
     * @param CreateAchievementUser $action
     * @return JsonResponse
     */
    public function storeAchievementUser(StoreAchievementUserRequest $request, CreateAchievementUser $action): JsonResponse
    {
        $achievementUser = $action($request->validated());

        return response()->json(
            new AchievementUserResource($achievementUser),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified achievement user.
     *
     * @param AchievementUser $achievementUser
     * @param ShowAchievementUser $action
     * @return JsonResponse
     */
    public function showAchievementUser(AchievementUser $achievementUser, ShowAchievementUser $action): JsonResponse
    {
        $achievementUser = $action($achievementUser);

        return response()->json(new AchievementUserResource($achievementUser));
    }

    /**
     * Update the specified achievement user in storage.
     *
     * @param UpdateAchievementUserRequest $request
     * @param AchievementUser $achievementUser
     * @param UpdateAchievementUser $action
     * @return JsonResponse
     */
    public function updateAchievementUser(UpdateAchievementUserRequest $request, AchievementUser $achievementUser, UpdateAchievementUser $action): JsonResponse
    {
        $achievementUser = $action($achievementUser, $request->validated());

        return response()->json(new AchievementUserResource($achievementUser));
    }

    /**
     * Remove the specified achievement user from storage.
     *
     * @param AchievementUser $achievementUser
     * @param DeleteAchievementUser $action
     * @return JsonResponse
     */
    public function destroyAchievementUser(AchievementUser $achievementUser, DeleteAchievementUser $action): JsonResponse
    {
        $action($achievementUser);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
