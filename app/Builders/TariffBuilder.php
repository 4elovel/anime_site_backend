<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;

class TariffBuilder extends Builder
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
     * Filter by active tariffs
     *
     * @return $this
     */
    public function active(): self
    {
        $this->where('is_active', true);

        return $this;
    }

    /**
     * Filter by inactive tariffs
     *
     * @return $this
     */
    public function inactive(): self
    {
        $this->where('is_active', false);

        return $this;
    }

    /**
     * Order by price
     *
     * @param string $direction
     * @return $this
     */
    public function orderByPrice(string $direction = 'asc'): self
    {
        $this->orderBy('price', $direction);

        return $this;
    }

    /**
     * Filter by price range
     *
     * @param float $min
     * @param float $max
     * @return $this
     */
    public function priceRange(float $min, float $max): self
    {
        $this->whereBetween('price', [$min, $max]);

        return $this;
    }

    /**
     * Filter by minimum price
     *
     * @param float $price
     * @return $this
     */
    public function minPrice(float $price): self
    {
        $this->where('price', '>=', $price);

        return $this;
    }

    /**
     * Filter by maximum price
     *
     * @param float $price
     * @return $this
     */
    public function maxPrice(float $price): self
    {
        $this->where('price', '<=', $price);

        return $this;
    }

    /**
     * Filter by duration
     *
     * @param int $days
     * @return $this
     */
    public function withDuration(int $days): self
    {
        $this->where('duration_days', $days);

        return $this;
    }

    /**
     * Filter by minimum duration
     *
     * @param int $days
     * @return $this
     */
    public function minDuration(int $days): self
    {
        $this->where('duration_days', '>=', $days);

        return $this;
    }

    /**
     * Filter by maximum duration
     *
     * @param int $days
     * @return $this
     */
    public function maxDuration(int $days): self
    {
        $this->where('duration_days', '<=', $days);

        return $this;
    }

    /**
     * Filter by tariffs with a specific feature
     *
     * @param string $feature
     * @return $this
     */
    public function withFeature(string $feature): self
    {
        $this->whereJsonContains('features', $feature);

        return $this;
    }

    /**
     * Filter by popular tariffs
     *
     * @return $this
     */
    public function popular(): self
    {
        $this->withCount('subscriptions')
            ->orderByDesc('subscriptions_count');

        return $this;
    }

    /**
     * Filter by recently created tariffs
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
     * Filter by recently updated tariffs
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
