<?php

namespace AnimeSite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
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
            'original_name' => $this->original_name,
            'full_name' => $this->full_name,
            'description' => $this->description,
            'type' => $this->type,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'photo' => $this->image,
            'animes' => AnimeResource::collection($this->whenLoaded('animes')),
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
