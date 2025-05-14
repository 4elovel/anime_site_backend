<?php

namespace AnimeSite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'parent_id' => $this->parent_id,
            'user_id' => $this->user_id,
            'commentable_id' => $this->commentable_id,
            'commentable_type' => $this->commentable_type,
            'is_spoiler' => $this->is_spoiler,
            'is_approved' => $this->is_approved,
            'user' => new UserResource($this->whenLoaded('user')),
            'parent' => new self($this->whenLoaded('parent')),
            'likes' => CommentLikeResource::collection($this->whenLoaded('likes')),
            'reports' => CommentReportResource::collection($this->whenLoaded('reports')),
            'likes_count' => $this->when(isset($this->likes_count), $this->likes_count),
            'dislikes_count' => $this->when(isset($this->dislikes_count), $this->dislikes_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
