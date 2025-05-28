<?php

namespace AnimeSite\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use AnimeSite\Actions\Notifications\ClearNotifications;
use AnimeSite\Actions\Notifications\DeleteNotification;
use AnimeSite\Actions\Notifications\GetAllNotifications;
use AnimeSite\Actions\Notifications\GetUnreadNotifications;
use AnimeSite\Actions\Notifications\MarkAllNotificationsAsRead;
use AnimeSite\Actions\Notifications\MarkNotificationAsRead;
use AnimeSite\Http\Controllers\Controller;
use AnimeSite\Http\Resources\NotificationResource;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, GetAllNotifications $action): JsonResponse
    {
        $paginated = $action($request);

        return response()->json([
            'data' => NotificationResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Get unread notifications for the authenticated user.
     */
    public function unread(Request $request, GetUnreadNotifications $action): JsonResponse
    {
        $paginated = $action($request);
        
        return response()->json([
            'data' => NotificationResource::collection($paginated),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }
    
    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $notification, MarkNotificationAsRead $action): JsonResponse
    {
        $action($notification);
        
        return response()->json(['message' => 'Notification marked as read']);
    }
    
    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(MarkAllNotificationsAsRead $action): JsonResponse
    {
        $action();
        
        return response()->json(['message' => 'All notifications marked as read']);
    }
    
    /**
     * Remove the specified notification.
     */
    public function destroy(string $notification, DeleteNotification $action): JsonResponse
    {
        $action($notification);
        
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
    
    /**
     * Clear all notifications for the authenticated user.
     */
    public function clear(ClearNotifications $action): JsonResponse
    {
        $action();
        
        return response()->json(['message' => 'All notifications cleared']);
    }
}
