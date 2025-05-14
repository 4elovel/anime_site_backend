<?php

namespace AnimeSite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
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
            'listable_id' => $this->listable_id,
            'listable_type' => $this->listable_type,
            'type' => $this->type,
            'user' => new UserResource($this->whenLoaded('user')),
            'listable' => $this->when($this->listable, function () {
                $resourceClass = match ($this->listable_type) {
                    'AnimeSite\\Models\\Anime' => AnimeResource::class,
                    'AnimeSite\\Models\\Episode' => EpisodeResource::class,
                    'AnimeSite\\Models\\Person' => PersonResource::class,
                    'AnimeSite\\Models\\Tag' => TagResource::class,
                    'AnimeSite\\Models\\Selection' => SelectionResource::class,
                    default => JsonResource::class,
                };

                return new $resourceClass($this->listable);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
