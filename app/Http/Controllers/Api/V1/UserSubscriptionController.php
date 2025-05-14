<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AnimeSite\Actions\UserSubscriptions\CancelSubscription;
use AnimeSite\Actions\UserSubscriptions\CreateUserSubscription;
use AnimeSite\Actions\UserSubscriptions\GetActiveSubscriptions;
use AnimeSite\Actions\UserSubscriptions\GetAllUserSubscriptions;
use AnimeSite\Actions\UserSubscriptions\RenewSubscription;
use AnimeSite\Actions\UserSubscriptions\ShowUserSubscription;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Requests\StoreUserSubscriptionRequest;
use AnimeSite\Http\Resources\UserSubscriptionResource;
use AnimeSite\Models\UserSubscription;

class UserSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     */
    public function show(UserSubscription $subscription, ShowUserSubscription $action): JsonResponse
    {
        $subscription = $action($subscription);
        
        return response()->json(new UserSubscriptionResource($subscription));
    }

    /**
     * Get active subscriptions for the authenticated user.
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
     * Cancel a subscription (disable auto-renew).
     */
    public function cancel(UserSubscription $subscription, CancelSubscription $action): JsonResponse
    {
        $subscription = $action($subscription);
        
        return response()->json(new UserSubscriptionResource($subscription));
    }
    
    /**
     * Renew a subscription (enable auto-renew).
     */
    public function renew(UserSubscription $subscription, RenewSubscription $action): JsonResponse
    {
        $subscription = $action($subscription);
        
        return response()->json(new UserSubscriptionResource($subscription));
    }
}
