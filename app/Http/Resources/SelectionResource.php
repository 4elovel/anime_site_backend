<?php

namespace AnimeSite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use AnimeSite\Http\Resources\AnimeResource;
use AnimeSite\Http\Resources\EpisodeResource;
use AnimeSite\Http\Resources\PersonResource;
use AnimeSite\Http\Resources\UserListResource;
use AnimeSite\Http\Resources\UserResource;

class SelectionResource extends JsonResource
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
            'poster' => $this->poster,
            'user_id' => $this->user_id,
            'is_published' => $this->is_published,
            'is_active' => $this->is_active,
            'user' => new UserResource($this->whenLoaded('user')),
            'animes_count' => $this->whenCounted('animes'),
            'animes' => AnimeResource::collection($this->whenLoaded('animes')),
            'persons_count' => $this->whenCounted('persons'),
            'persons' => PersonResource::collection($this->whenLoaded('persons')),
            'episodes_count' => $this->whenCounted('episodes'),
            'episodes' => EpisodeResource::collection($this->whenLoaded('episodes')),
            'user_lists_count' => $this->whenCounted('userLists'),
            'user_lists' => UserListResource::collection($this->whenLoaded('userLists')),
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
