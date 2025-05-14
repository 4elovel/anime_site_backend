<?php

namespace AnimeSite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AchievementResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'max_counts' => $this->max_counts,
            'users' => UserResource::collection($this->whenLoaded('users')),
            'progress_count' => $this->whenPivotLoaded('achievement_user', function () {
                return $this->pivot->progress_count;
            }),
        ];
    }
}
