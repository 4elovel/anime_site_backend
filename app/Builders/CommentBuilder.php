<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;

class CommentBuilder extends Builder
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
     * Filter by replies (comments with parent_id)
     *
     * @return $this
     */
    public function replies(): self
    {
        $this->whereNotNull('parent_id');

        return $this;
    }

    /**
     * Filter by root comments (comments without parent_id)
     *
     * @return $this
     */
    public function roots(): self
    {
        $this->whereNull('parent_id');

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
     * Filter by commentable type
     *
     * @param string $type
     * @return $this
     */
    public function forCommentableType(string $type): self
    {
        $this->where('commentable_type', $type);

        return $this;
    }

    /**
     * Filter by commentable ID
     *
     * @param string $id
     * @return $this
     */
    public function forCommentableId(string $id): self
    {
        $this->where('commentable_id', $id);

        return $this;
    }

    /**
     * Filter by commentable
     *
     * @param string $type
     * @param string $id
     * @return $this
     */
    public function forCommentable(string $type, string $id): self
    {
        $this->where('commentable_type', $type)
            ->where('commentable_id', $id);

        return $this;
    }

    /**
     * Filter by parent comment
     *
     * @param string $parentId
     * @return $this
     */
    public function forParent(string $parentId): self
    {
        $this->where('parent_id', $parentId);

        return $this;
    }

    /**
     * Filter by comments with likes
     *
     * @param int $minLikes
     * @return $this
     */
    public function withLikes(int $minLikes = 1): self
    {
        $this->has('likes', '>=', $minLikes);

        return $this;
    }

    /**
     * Filter by comments with reports
     *
     * @param int $minReports
     * @return $this
     */
    public function withReports(int $minReports = 1): self
    {
        $this->has('reports', '>=', $minReports);

        return $this;
    }

    /**
     * Order by likes count
     *
     * @param string $direction
     * @return $this
     */
    public function orderByLikes(string $direction = 'desc'): self
    {
        $this->withCount('likes')
            ->orderBy('likes_count', $direction);

        return $this;
    }

    /**
     * Order by reports count
     *
     * @param string $direction
     * @return $this
     */
    public function orderByReports(string $direction = 'desc'): self
    {
        $this->withCount('reports')
            ->orderBy('reports_count', $direction);

        return $this;
    }

    /**
     * Search in comment body
     *
     * @param string $term
     * @return $this
     */
    public function search(string $term): self
    {
        $this->where('body', 'LIKE', "%{$term}%");

        return $this;
    }
}
