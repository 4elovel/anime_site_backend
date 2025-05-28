<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use AnimeSite\Actions\Users\CreateUser;
use AnimeSite\Actions\Users\DeleteUser;
use AnimeSite\Actions\Users\GetAllUsers;
use AnimeSite\Actions\Users\GetUserProfile;
use AnimeSite\Actions\Users\GetUserSettings;
use AnimeSite\Actions\Users\ShowUser;
use AnimeSite\Actions\Users\UpdateUser;
use AnimeSite\Actions\Users\UpdateUserProfile;
use AnimeSite\Actions\Users\UpdateUserSettings;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StoreUserRequest;
use AnimeSite\Http\Requests\UpdateUserProfileRequest;
use AnimeSite\Http\Requests\UpdateUserRequest;
use AnimeSite\Http\Requests\UpdateUserSettingsRequest;
use AnimeSite\Http\Requests\UploadUserAvatarRequest;
use AnimeSite\Http\Requests\UploadUserBackdropRequest;
use AnimeSite\Actions\Users\UploadUserAvatar;
use AnimeSite\Actions\Users\UploadUserBackdrop;
use AnimeSite\Http\Resources\UserResource;
use AnimeSite\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     *
     * @param Request $request
     * @param GetAllUsers $action
     * @return JsonResponse
     */
    public function index(Request $request, GetAllUsers $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => UserResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param StoreUserRequest $request
     * @param CreateUser $action
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request, CreateUser $action): JsonResponse
    {
        $user = $action($request->validated());

        return response()->json(
            new UserResource($user),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the authenticated user.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = Auth::user();

        return response()->json(new UserResource($user));
    }

    /**
     * Display the specified user.
     *
     * @param User $user
     * @param ShowUser $action
     * @return JsonResponse
     */
    public function show(User $user, ShowUser $action): JsonResponse
    {
        $user = $action($user);

        return response()->json(new UserResource($user));
    }

    /**
     * Update the authenticated user.
     *
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function updateMe(UpdateUserRequest $request): JsonResponse
    {
        $user = Auth::user();
        $action = app(UpdateUser::class);
        $user = $action($user, $request->validated());

        return response()->json(new UserResource($user));
    }

    /**
     * Update the specified user in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @param UpdateUser $action
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUser $action): JsonResponse
    {
        $user = $action($user, $request->validated());

        return response()->json(new UserResource($user));
    }

    /**
     * Remove the specified user from storage.
     *
     * @param User $user
     * @param DeleteUser $action
     * @return JsonResponse
     */
    public function destroy(User $user, DeleteUser $action): JsonResponse
    {
        $action($user);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get user settings.
     *
     * @param GetUserSettings $action
     * @return JsonResponse
     */
    public function settings(GetUserSettings $action): JsonResponse
    {
        $settings = $action(Auth::user());

        return response()->json(['data' => $settings]);
    }

    /**
     * Update user settings.
     *
     * @param UpdateUserSettingsRequest $request
     * @param UpdateUserSettings $action
     * @return JsonResponse
     */
    public function updateSettings(UpdateUserSettingsRequest $request, UpdateUserSettings $action): JsonResponse
    {
        $settings = $action(Auth::user(), $request->validated());

        return response()->json(['data' => $settings]);
    }

    /**
     * Get user profile.
     *
     * @param GetUserProfile $action
     * @return JsonResponse
     */
    public function profile(GetUserProfile $action): JsonResponse
    {
        $profile = $action(Auth::user());

        return response()->json(['data' => $profile]);
    }

    /**
     * Update user profile.
     *
     * @param UpdateUserProfileRequest $request
     * @param UpdateUserProfile $action
     * @return JsonResponse
     */
    public function updateProfile(UpdateUserProfileRequest $request, UpdateUserProfile $action): JsonResponse
    {
        $profile = $action(Auth::user(), $request->validated());

        return response()->json(['data' => $profile]);
    }

    /**
     * Upload user avatar.
     *
     * @param UploadUserAvatarRequest $request
     * @param UploadUserAvatar $action
     * @return JsonResponse
     */
    public function uploadAvatar(UploadUserAvatarRequest $request, UploadUserAvatar $action): JsonResponse
    {
        $avatarUrl = $action(Auth::user(), $request->file('avatar'));

        return response()->json([
            'data' => [
                'avatar' => $avatarUrl
            ]
        ]);
    }

    /**
     * Upload user backdrop.
     *
     * @param UploadUserBackdropRequest $request
     * @param UploadUserBackdrop $action
     * @return JsonResponse
     */
    public function uploadBackdrop(UploadUserBackdropRequest $request, UploadUserBackdrop $action): JsonResponse
    {
        $backdropUrl = $action(Auth::user(), $request->file('backdrop'));

        return response()->json([
            'data' => [
                'backdrop' => $backdropUrl
            ]
        ]);
    }
}
