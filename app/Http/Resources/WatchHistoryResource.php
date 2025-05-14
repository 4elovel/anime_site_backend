<?php

namespace AnimeSite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WatchHistoryResource extends JsonResource
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
            'episode_id' => $this->episode_id,
            'progress_time' => $this->progress_time,
            'user' => new UserResource($this->whenLoaded('user')),
            'episode' => new EpisodeResource($this->whenLoaded('episode')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
