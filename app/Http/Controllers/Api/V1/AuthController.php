<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use AnimeSite\Actions\Auth\ForgotPassword;
use AnimeSite\Actions\Auth\GetCurrentUser;
use AnimeSite\Actions\Auth\LoginUser;
use AnimeSite\Actions\Auth\LogoutUser;
use AnimeSite\Actions\Auth\RegisterUser;
use AnimeSite\Actions\Auth\ResetPassword;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\ForgotPasswordRequest;
use AnimeSite\Http\Requests\LoginRequest;
use AnimeSite\Http\Requests\RegisterRequest;
use AnimeSite\Http\Requests\ResetPasswordRequest;
use AnimeSite\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @param RegisterUser $action
     * @return JsonResponse
     */
    public function register(RegisterRequest $request, RegisterUser $action): JsonResponse
    {
        $result = $action($request->validated());

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], ResponseAlias::HTTP_CREATED);
    }

    /**
     * Login a user.
     *
     * @param LoginRequest $request
     * @param LoginUser $action
     * @return JsonResponse
     */
    public function login(LoginRequest $request, LoginUser $action): JsonResponse
    {
        $result = $action($request->validated());

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    /**
     * Logout the current user.
     *
     * @param LogoutUser $action
     * @return JsonResponse
     */
    public function logout(LogoutUser $action): JsonResponse
    {
        $action(Auth::user());

        return response()->json([
            'message' => 'Успішний вихід з системи',
        ]);
    }

    /**
     * Get the authenticated user.
     *
     * @param GetCurrentUser $action
     * @return JsonResponse
     */
    public function user(GetCurrentUser $action): JsonResponse
    {
        $user = $action(Auth::user());

        return response()->json(new UserResource($user));
    }

    /**
     * Send a password reset link.
     *
     * @param ForgotPasswordRequest $request
     * @param ForgotPassword $action
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request, ForgotPassword $action): JsonResponse
    {
        $action($request->validated());

        return response()->json([
            'message' => 'Посилання для відновлення пароля надіслано на вашу електронну адресу',
        ]);
    }

    /**
     * Reset the user's password.
     *
     * @param ResetPasswordRequest $request
     * @param ResetPassword $action
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request, ResetPassword $action): JsonResponse
    {
        $action($request->validated());

        return response()->json([
            'message' => 'Пароль успішно змінено',
        ]);
    }
}
