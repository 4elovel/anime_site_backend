<?php

namespace Liamtseva\Cinema\Http\Controllers;

use Illuminate\Http\Request;
use Liamtseva\Cinema\Models\Anime;

class AnimeNotificationController extends Controller
{
    public function subscribe(Request $request, Anime $anime)
    {
        $user = $request->user();
        
        if (!$user->subscribedAnime()->where('anime_id', $anime->id)->exists()) {
            $user->subscribedAnime()->attach($anime->id);
            return response()->json(['message' => 'Підписка на оновлення успішно оформлена']);
        }
        
        return response()->json(['message' => 'Ви вже підписані на це аніме']);
    }
    
    public function unsubscribe(Request $request, Anime $anime)
    {
        $user = $request->user();
        
        $user->subscribedAnime()->detach($anime->id);
        
        return response()->json(['message' => 'Підписка скасована']);
    }
    
    public function getSubscriptions(Request $request)
    {
        $user = $request->user();
        
        $subscriptions = $user->subscribedAnime()->get();
        
        return response()->json(['subscriptions' => $subscriptions]);
    }
    
    public function getNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->paginate(15);
        
        return response()->json($notifications);
    }
    
    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return response()->json(['message' => 'Notification marked as read']);
    }
    
    public function markAllAsRead(Request $request)
    {
        $request->user()->notifications()->update(['read_at' => now()]);
        
        return response()->json(['message' => 'All notifications marked as read']);
    }
}