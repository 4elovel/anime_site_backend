<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;
use AnimeSite\Enums\CommentReportType;

class CommentReportBuilder extends Builder
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
     * Filter by report type
     *
     * @param CommentReportType $type
     * @return $this
     */
    public function ofType(CommentReportType $type): self
    {
        $this->where('type', $type->value);

        return $this;
    }

    /**
     * Filter by viewed status
     *
     * @param bool $isViewed
     * @return $this
     */
    public function viewed(bool $isViewed = true): self
    {
        $this->where('is_viewed', $isViewed);

        return $this;
    }

    /**
     * Filter by unviewed reports
     *
     * @return $this
     */
    public function unViewed(): self
    {
        $this->where('is_viewed', false);

        return $this;
    }

    /**
     * Filter by reports created after a specific date
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
     * Filter by reports created before a specific date
     *
     * @param string $date
     * @return $this
     */
    public function before(string $date): self
    {
        $this->where('created_at', '<=', $date);

        return $this;
    }
}
