<?php

namespace Liamtseva\Cinema\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(15);

        return response()->json([
            'notifications' => $notifications->items(),
            'pagination' => [
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
            ]
        ]);
    }

    public function show($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'notification' => $notification,
            'redirect' => $this->getRedirectUrl($notification)
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification
        ]);
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->update(['read_at' => now()]);

        return response()->json([
            'message' => 'All notifications marked as read'
        ]);
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();

        return response()->json([
            'count' => $count
        ]);
    }

    private function getRedirectUrl($notification)
    {
        // Return appropriate frontend routes based on notification type
        if ($notification->type === 'new_episode') {
            $data = $notification->data;
            return "/anime/{$data['anime_id']}/episode/{$data['episode_id']}";
        }

        return '/notifications';
    }
}
