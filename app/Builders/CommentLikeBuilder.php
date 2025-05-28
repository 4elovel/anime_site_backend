<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;

class CommentLikeBuilder extends Builder
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
    public function byUser(string $userId): self
    {
        $this->where('user_id', $userId);

        return $this;
    }

    /**
     * Filter by comment
     *
     * @param string $commentId
     * @return $this
     */
    public function byComment(string $commentId): self
    {
        $this->where('comment_id', $commentId);

        return $this;
    }

    /**
     * Filter by likes created after a specific date
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
     * Filter by likes created before a specific date
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
     * Filter by likes created within a date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return $this
     */
    public function between(string $startDate, string $endDate): self
    {
        $this->whereBetween('created_at', [$startDate, $endDate]);

        return $this;
    }

    /**
     * Filter by likes only (not dislikes)
     *
     * @return $this
     */
    public function onlyLikes(): self
    {
        $this->where('is_liked', true);

        return $this;
    }

    /**
     * Filter by dislikes only (not likes)
     *
     * @return $this
     */
    public function onlyDislikes(): self
    {
        $this->where('is_liked', false);

        return $this;
    }
}
