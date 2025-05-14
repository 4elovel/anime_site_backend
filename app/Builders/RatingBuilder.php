<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;

class RatingBuilder extends Builder
{
    /**
     * Order by created date
     *
     * @param string $direction
     * @return $this
     */
    public function orderByCreatedAt(string $direction = 'desc'): self
    {
        $this->orderBy('created_at', $direction);

        return $this;
    }

    /**
     * Order by updated date
     *
     * @param string $direction
     * @return $this
     */
    public function orderByUpdatedAt(string $direction = 'desc'): self
    {
        $this->orderBy('updated_at', $direction);

        return $this;
    }

    /**
     * Filter by user
     *
     * @param string $userId
     * @return $this
     */
    public function forUser(string $userId): self
    {
        $this->where('user_id', $userId);

        return $this;
    }

    /**
     * Filter by anime
     *
     * @param string $animeId
     * @return $this
     */
    public function forAnime(string $animeId): self
    {
        $this->where('anime_id', $animeId);

        return $this;
    }

    /**
     * Filter by minimum rating value
     *
     * @param int $value
     * @return $this
     */
    public function withMinRating(int $value): self
    {
        $this->where('rating', '>=', $value);

        return $this;
    }

    /**
     * Filter by maximum rating value
     *
     * @param int $value
     * @return $this
     */
    public function withMaxRating(int $value): self
    {
        $this->where('rating', '<=', $value);

        return $this;
    }

    /**
     * Filter by rating range
     *
     * @param int $min
     * @param int $max
     * @return $this
     */
    public function withRatingRange(int $min, int $max): self
    {
        $this->whereBetween('rating', [$min, $max]);

        return $this;
    }

    /**
     * Filter by ratings with reviews
     *
     * @return $this
     */
    public function withReviews(): self
    {
        $this->whereNotNull('review')
            ->where('review', '!=', '');

        return $this;
    }

    /**
     * Filter by ratings without reviews
     *
     * @return $this
     */
    public function withoutReviews(): self
    {
        $this->where(function ($query) {
            $query->whereNull('review')
                ->orWhere('review', '');
        });

        return $this;
    }

    /**
     * Search in reviews
     *
     * @param string $term
     * @return $this
     */
    public function searchInReviews(string $term): self
    {
        $this->where('review', 'LIKE', "%{$term}%");

        return $this;
    }

    /**
     * Order by rating value
     *
     * @param string $direction
     * @return $this
     */
    public function orderByRating(string $direction = 'desc'): self
    {
        $this->orderBy('rating', $direction);

        return $this;
    }

    /**
     * Order by review length
     *
     * @param string $direction
     * @return $this
     */
    public function orderByReviewLength(string $direction = 'desc'): self
    {
        $this->orderByRaw('LENGTH(review) ' . $direction);

        return $this;
    }

    /**
     * Filter by recently created ratings
     *
     * @param int $days
     * @return $this
     */
    public function createdInLastDays(int $days = 30): self
    {
        $this->where('created_at', '>=', now()->subDays($days))
            ->orderByDesc('created_at');

        return $this;
    }

    /**
     * Filter by recently updated ratings
     *
     * @param int $days
     * @return $this
     */
    public function updatedInLastDays(int $days = 30): self
    {
        $this->where('updated_at', '>=', now()->subDays($days))
            ->orderByDesc('updated_at');

        return $this;
    }
}
