<?php

namespace AnimeSite\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimeResource extends JsonResource
{
    /**
     * Перетворити ресурс в масив.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'original_name' => $this->name,
            'description' => $this->description,
            'poster' => $this->poster,
            'poster_url' => $this->when($this->poster, fn() => $this->getFileUrl($this->poster)),
            'kind' => $this->kind,
            'status' => $this->status,
            'period' => $this->period,
            'restricted_rating' => $this->restricted_rating,
            'source' => $this->source,
            'countries' => $this->countries,
            'duration' => $this->duration,
            'episodes_count' => $this->episodes_count,
            'first_air_date' => $this->first_air_date,
            'last_air_date' => $this->last_air_date,
            'imdb_score' => $this->imdb_score,
            'user_rating_avg' => $this->whenHas('ratings_avg_number'),
            'aliases' => $this->aliases,
            'attachments' => $this->attachments,
            'related' => $this->related,
            'similars' => $this->similars,
            'api_sources' => $this->api_sources,
            'studio' => new StudioResource($this->whenLoaded('studio')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'genres' => TagResource::collection($this->whenLoaded('genres')),
            'people' => PersonResource::collection($this->whenLoaded('people')),
            'episodes' => EpisodeResource::collection($this->whenLoaded('episodes')),
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'selections' => SelectionResource::collection($this->whenLoaded('selections')),
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
