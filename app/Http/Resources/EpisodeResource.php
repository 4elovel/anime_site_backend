<?php

namespace AnimeSite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
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
            'number' => $this->number,
            'duration' => $this->duration,
            'air_date' => $this->air_date,
            'poster' => $this->poster,
            'anime_id' => $this->anime_id,
            'anime' => new AnimeResource($this->whenLoaded('anime')),
            'video_players' => $this->video_players,
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
                'image' => $this->meta_image,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
