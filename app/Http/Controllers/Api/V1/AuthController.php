<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use AnimeSite\DTOs\Auth\AuthResponseDTO;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\Auth\ForgotPasswordRequest;
use AnimeSite\Http\Requests\Auth\LoginRequest;
use AnimeSite\Http\Requests\Auth\RegisterRequest;
use AnimeSite\Http\Requests\Auth\ResetPasswordRequest;
use AnimeSite\Http\Resources\UserResource;
use AnimeSite\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $dto = $request->toDTO();

            // Create the user
            $user = User::create([
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => Hash::make($dto->password),
            ]);

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Login the user
            Auth::login($user);

            // Create response DTO
            $responseDTO = new AuthResponseDTO(
                user: $user,
                token: $token,
                message: 'Registration successful'
            );

            return response()->json([
                'message' => $responseDTO->message,
                'user' => new UserResource($responseDTO->user),
                'token' => $responseDTO->token,
            ], ResponseAlias::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Login a user.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $dto = $request->toDTO();

            // Attempt to authenticate
            if (!Auth::attempt(['email' => $dto->email, 'password' => $dto->password])) {
                return response()->json([
                    'message' => 'The provided credentials are incorrect.'
                ], ResponseAlias::HTTP_UNAUTHORIZED);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            // Create response DTO
            $responseDTO = new AuthResponseDTO(
                user: $user,
                token: $token,
                message: 'Login successful'
            );

            return response()->json([
                'message' => $responseDTO->message,
                'user' => new UserResource($responseDTO->user),
                'token' => $responseDTO->token,
            ]);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Logout the current user.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            // Revoke the token that was used to authenticate the current request
            if (Auth::check()) {
                Auth::user()->currentAccessToken()->delete();
            }

            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return response()->json([
                'message' => 'Успішний вихід з системи',
            ]);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get the authenticated user.
     *
     * @return JsonResponse
     */
    public function user(): JsonResponse
    {
        try {
            $user = Auth::user();

            return response()->json(new UserResource($user));
        } catch (\Exception $e) {
            Log::error('User fetch error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch user data',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Send a password reset link.
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            // Send password reset link
            $status = Password::sendResetLink($request->validated());

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'message' => 'Посилання для відновлення пароля надіслано на вашу електронну адресу',
                ]);
            }

            return response()->json([
                'message' => __($status)
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            Log::error('Forgot password error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send password reset link',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Reset the user's password.
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            // Reset password
            $status = Password::reset(
                $request->validated(),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'message' => 'Пароль успішно змінено',
                ]);
            }

            return response()->json([
                'message' => __($status)
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            Log::error('Reset password error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to reset password',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
