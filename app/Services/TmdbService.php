<?php

namespace AnimeSite\Services;

use AnimeSite\Enums\ApiSourceName;
use AnimeSite\Enums\Kind;
use AnimeSite\Enums\RestrictedRating;
use AnimeSite\Enums\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TmdbService
{
    protected string $apiKey;
    protected string $baseUrl = "https://api.themoviedb.org/3";
    protected string $imageBaseUrl = "https://image.tmdb.org/t/p/original";

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.key');
    }

    /**
     * Get anime (TV show) data by TMDB ID
     *
     * @param int $id TMDB TV show ID
     * @return array|null The TV show data or null on failure
     */
    public function getAnimeById($id)
    {
        try {
            $response = Http::get("{$this->baseUrl}/tv/{$id}", [
                'api_key' => $this->apiKey,
                'language' => 'en-US',
                'append_to_response' => 'credits,keywords,similar,external_ids,content_ratings'
            ]);

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            // Log error here if needed
            return null;
        }
    }

    /**
     * Get full image URL from path
     *
     * @param string|null $path Image path
     * @return string|null Full image URL or null
     */
    public function getPosterUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return "{$this->imageBaseUrl}{$path}";
    }

    /**
     * Map TMDB status to application Status enum
     *
     * @param string $tmdbStatus
     * @return Status
     */
    public function mapStatus(string $tmdbStatus): Status
    {
        return match (strtolower($tmdbStatus)) {
            'returning series' => Status::ONGOING,
            'ended' => Status::RELEASED,
            'canceled' => Status::CANCELED,
            'in production' => Status::ANONS,
            default => Status::ANONS,
        };
    }

    /**
     * Map TMDB content rating to application RestrictedRating enum
     *
     * @param array $data TMDB response data
     * @return RestrictedRating|null
     */
    public function mapAgeRating(array $data): ?RestrictedRating
    {
        if (!isset($data['content_ratings']['results']) || empty($data['content_ratings']['results'])) {
            return null;
        }
        $ratingItem = null;
        foreach ($data['content_ratings']['results'] as $rating) {
            if ($rating['iso_3166_1'] === 'US') {
                $ratingItem = $rating;
                break;
            } else if ($rating['iso_3166_1'] === 'JP') {
                $ratingItem = $rating;
                break;
            }
        }
        if (!$ratingItem && !empty($data['content_ratings']['results'])) {
            $ratingItem = $data['content_ratings']['results'][0];
        }

        if (!$ratingItem || !isset($ratingItem['rating'])) {
            return null;
        }
        $rating = $ratingItem['rating'];
        return match (strtoupper($rating)) {
            'G', 'TV-G', 'TV-Y', 'TV-Y7' => RestrictedRating::G,
            'PG', 'TV-PG' => RestrictedRating::PG,
            'PG-13', 'TV-14' => RestrictedRating::PG_13,
            'R', 'TV-MA', 'MA' => RestrictedRating::R,
            'NC-17', => RestrictedRating::NC_17,
            default => null,
        };
    }

    /**
     * Map TMDB type to application Kind enum
     *
     * @param array $data TMDB show data
     * @return Kind
     */
    public function determineKind(array $data): Kind
    {
        $episodeCount = $data['number_of_episodes'] ?? 0;
        $seasonsCount = $data['number_of_seasons'] ?? 0;
        if ($episodeCount <= 3 && $seasonsCount <= 1) {
            return Kind::OVA;
        } elseif (isset($data['type']) && strtolower($data['type']) === 'miniseries') {
            return Kind::TV_SPECIAL;
        } else {
            return Kind::TV_SERIES;
        }
    }

    /**
     * Get formatted API data for filling form
     *
     * @param array $data Raw TMDB response
     * @return array Formatted data for form
     */
    public function formatDataForForm(array $data): array
    {
        // Format basic data
        $formattedData = [
            'name' => $data['name'] ?? '',
            'description' => $data['overview'] ?? '',
            'first_air_date' => !empty($data['first_air_date']) ? Carbon::parse($data['first_air_date'])->format('Y-m-d') : null,
            'last_air_date' => !empty($data['last_air_date']) ? Carbon::parse($data['last_air_date'])->format('Y-m-d') : null,
            'poster' => $this->getPosterUrl($data['poster_path'] ?? null),
            'image_name' => $this->getPosterUrl($data['backdrop_path'] ?? $data['poster_path'] ?? null),
            'aliases' => isset($data['original_name']) && $data['original_name'] !== ($data['name'] ?? '')
                ? [$data['original_name']]
                : [],
            'episodes_count' => $data['number_of_episodes'] ?? null,
            'duration' => isset($data['episode_run_time']) && !empty($data['episode_run_time'])
                ? $data['episode_run_time'][0]
                : null,
            'kind' => $this->determineKind($data)->value,
            'status' => $this->mapStatus($data['status'] ?? '')->value,
            'api_sources'=> [
                [
                    'source' => ApiSourceName::TMDB->value,
                    'id' => $data['id'],
                ]
            ],
        ];

        if (isset($data['origin_country']) && is_array($data['origin_country'])) {
            $formattedData['countries'] = collect($data['origin_country'])
                ->map(fn($c) => ['countries' => $c])
                ->toArray();
        } else {
            $formattedData['countries'] = [];
        }

        if (isset($data['images']['backdrops']) && is_array($data['images']['backdrops'])) {
            $formattedData['attachments'] = collect($data['images']['backdrops'])
                ->take(5)
                ->map(function($image) {
                    return [
                        'type' => 'picture',
                        'src' => $this->getPosterUrl($image['file_path'] ?? null)
                    ];
                })
                ->filter(fn($item) => !empty($item['src']))
                ->values()
                ->toArray();
        } else {
            if (!empty($formattedData['poster'])) {
                $formattedData['attachments'] = [
                    [
                        'type' => 'picture',
                        'src' => $formattedData['poster']
                    ]
                ];
            } else {
                $formattedData['attachments'] = [];
            }
        }

        if (isset($data['vote_average'])) {
            $formattedData['scores'] = [
                [
                    'source' => ApiSourceName::TMDB->value,
                    'value' => $data['vote_average'] * 10
                ]
            ];
        } else {
            $formattedData['scores'] = [];
        }

        return $formattedData;
    }
}
