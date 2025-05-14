<?php

namespace AnimeSite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
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
            'anime_id' => $this->anime_id,
            'score' => $this->score,
            'review' => $this->review,
            'user' => new UserResource($this->whenLoaded('user')),
            'anime' => new AnimeResource($this->whenLoaded('anime')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
