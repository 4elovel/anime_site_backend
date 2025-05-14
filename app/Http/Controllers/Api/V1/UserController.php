<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use AnimeSite\Actions\Users\DeleteUser;
use AnimeSite\Actions\Users\GetUserProfile;
use AnimeSite\Actions\Users\GetUserSettings;
use AnimeSite\Actions\Users\ShowUser;
use AnimeSite\Actions\Users\UpdateUser;
use AnimeSite\Actions\Users\UpdateUserProfile;
use AnimeSite\Actions\Users\UpdateUserSettings;
use AnimeSite\Http\Controllers\Controller;
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
     * Display the authenticated user.
     */
    public function me(): JsonResponse
    {
        $user = Auth::user();

        return response()->json(new UserResource($user));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, ShowUser $action): JsonResponse
    {
        $user = $action($user);

        return response()->json(new UserResource($user));
    }

    /**
     * Update the authenticated user.
     */
    public function updateMe(UpdateUserRequest $request): JsonResponse
    {
        $user = Auth::user();
        $action = app(UpdateUser::class);
        $user = $action($user, $request->validated());

        return response()->json(new UserResource($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUser $action): JsonResponse
    {
        $user = $action($user, $request->validated());

        return response()->json(new UserResource($user));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, DeleteUser $action): JsonResponse
    {
        $action($user);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get user settings.
     */
    public function settings(GetUserSettings $action): JsonResponse
    {
        $settings = $action(Auth::user());

        return response()->json(['data' => $settings]);
    }

    /**
     * Update user settings.
     */
    public function updateSettings(UpdateUserSettingsRequest $request, UpdateUserSettings $action): JsonResponse
    {
        $settings = $action(Auth::user(), $request->validated());

        return response()->json(['data' => $settings]);
    }

    /**
     * Get user profile.
     */
    public function profile(GetUserProfile $action): JsonResponse
    {
        $profile = $action(Auth::user());

        return response()->json(['data' => $profile]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(UpdateUserProfileRequest $request, UpdateUserProfile $action): JsonResponse
    {
        $profile = $action(Auth::user(), $request->validated());

        return response()->json(['data' => $profile]);
    }

    /**
     * Upload user avatar.
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
