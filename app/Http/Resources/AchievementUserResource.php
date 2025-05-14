<?php

namespace AnimeSite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AchievementUserResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'achievement_id' => $this->achievement_id,
            'progress_count' => $this->progress_count,
            'user' => new UserResource($this->whenLoaded('user')),
            'achievement' => new AchievementResource($this->whenLoaded('achievement')),
        ];
    }
}
