<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\UserSubscriptions\CancelSubscription;
use AnimeSite\Actions\UserSubscriptions\CreateUserSubscription;
use AnimeSite\Actions\UserSubscriptions\DeactivateSubscription;
use AnimeSite\Actions\UserSubscriptions\ExtendSubscription;
use AnimeSite\Actions\UserSubscriptions\GetActiveSubscriptions;
use AnimeSite\Actions\UserSubscriptions\GetAllUserSubscriptions;
use AnimeSite\Actions\UserSubscriptions\RenewSubscription;
use AnimeSite\Actions\UserSubscriptions\ShowUserSubscription;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\ExtendSubscriptionRequest;
use AnimeSite\Http\Requests\StoreUserSubscriptionRequest;
use AnimeSite\Http\Resources\UserSubscriptionResource;
use AnimeSite\Models\UserSubscription;

class UserSubscriptionController extends Controller
{
    /**
     * Отримати список підписок користувачів.
     *
     * @param Request $request
     * @param GetAllUserSubscriptions $action
     * @return JsonResponse
     */
    public function index(Request $request, GetAllUserSubscriptions $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => UserSubscriptionResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Створити нову підписку користувача.
     *
     * @param StoreUserSubscriptionRequest $request
     * @param CreateUserSubscription $action
     * @return JsonResponse
     */
    public function store(StoreUserSubscriptionRequest $request, CreateUserSubscription $action): JsonResponse
    {
        $subscription = $action($request->validated());

        return response()->json(
            new UserSubscriptionResource($subscription),
            Response::HTTP_CREATED
        );
    }

    /**
     * Отримати інформацію про конкретну підписку.
     *
     * @param UserSubscription $subscription
     * @param ShowUserSubscription $action
     * @return JsonResponse
     */
    public function show(UserSubscription $subscription, ShowUserSubscription $action): JsonResponse
    {
        $subscription = $action($subscription);

        return response()->json(new UserSubscriptionResource($subscription));
    }

    /**
     * Отримати активні підписки для авторизованого користувача.
     *
     * @param Request $request
     * @param GetActiveSubscriptions $action
     * @return JsonResponse
     */
    public function active(Request $request, GetActiveSubscriptions $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => UserSubscriptionResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Скасувати автоматичне продовження підписки.
     *
     * @param UserSubscription $subscription
     * @param CancelSubscription $action
     * @return JsonResponse
     */
    public function cancel(UserSubscription $subscription, CancelSubscription $action): JsonResponse
    {
        $subscription = $action($subscription);

        return response()->json(new UserSubscriptionResource($subscription));
    }

    /**
     * Увімкнути автоматичне продовження підписки.
     *
     * @param UserSubscription $subscription
     * @param RenewSubscription $action
     * @return JsonResponse
     */
    public function renew(UserSubscription $subscription, RenewSubscription $action): JsonResponse
    {
        $subscription = $action($subscription);

        return response()->json(new UserSubscriptionResource($subscription));
    }

    /**
     * Деактивувати підписку користувача.
     *
     * @param UserSubscription $subscription
     * @param DeactivateSubscription $action
     * @return JsonResponse
     */
    public function deactivate(UserSubscription $subscription, DeactivateSubscription $action): JsonResponse
    {
        $subscription = $action($subscription);

        return response()->json(new UserSubscriptionResource($subscription));
    }

    /**
     * Продовжити підписку користувача на вказану кількість днів.
     *
     * @param UserSubscription $subscription
     * @param ExtendSubscriptionRequest $request
     * @param ExtendSubscription $action
     * @return JsonResponse
     */
    public function extend(UserSubscription $subscription, ExtendSubscriptionRequest $request, ExtendSubscription $action): JsonResponse
    {
        $subscription = $action($subscription, $request->validated());

        return response()->json(new UserSubscriptionResource($subscription));
    }
}
