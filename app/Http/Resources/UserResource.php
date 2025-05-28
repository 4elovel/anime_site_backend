<?php

namespace AnimeSite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'avatar' => $this->avatar,
            'allow_adult' => $this->allow_adult,
            //'vip' => $this->vip, //TODO що таке віп
            'email_verified_at' => $this->email_verified_at,
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'comment_likes' => CommentLikeResource::collection($this->whenLoaded('commentLikes')),
            'comment_reports' => CommentReportResource::collection($this->whenLoaded('commentReports')),
            'user_lists' => UserListResource::collection($this->whenLoaded('userLists')),
            'search_histories' => SearchHistoryResource::collection($this->whenLoaded('searchHistories')),
            'watch_histories' => WatchHistoryResource::collection($this->whenLoaded('watchHistories')),
            'selections' => SelectionResource::collection($this->whenLoaded('selections')),
            'achievements' => AchievementResource::collection($this->whenLoaded('achievements')),
            'subscriptions' => UserSubscriptionResource::collection($this->whenLoaded('subscriptions')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
