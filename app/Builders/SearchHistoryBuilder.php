<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;

class SearchHistoryBuilder extends Builder
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
     * Filter by query term
     *
     * @param string $term
     * @return $this
     */
    public function withQuery(string $term): self
    {
        $this->where('query', 'LIKE', "%{$term}%");

        return $this;
    }

    /**
     * Filter by exact query
     *
     * @param string $query
     * @return $this
     */
    public function withExactQuery(string $query): self
    {
        $this->where('query', $query);

        return $this;
    }

    /**
     * Filter by searches created after a specific date
     *
     * @param string $date
     * @return $this
     */
    public function after(string $date): self
    {
        $this->where('created_at', '>=', $date);

        return $this;
    }

    /**
     * Filter by searches created before a specific date
     *
     * @param string $date
     * @return $this
     */
    public function before(string $date): self
    {
        $this->where('created_at', '<=', $date);

        return $this;
    }

    /**
     * Clean old search history for a user
     *
     * @param string $userId
     * @param int $days
     * @return int
     */
    public function cleanOldHistory(string $userId, int $days = 30): int
    {
        return $this->where('user_id', $userId)
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
    }
}
