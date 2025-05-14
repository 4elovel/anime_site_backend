<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;

class AchievementBuilder extends Builder
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
        $this->whereHas('users', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        });

        return $this;
    }

    /**
     * Filter by achievements with a minimum progress count
     *
     * @param int $count
     * @return $this
     */
    public function withMinProgressCount(int $count): self
    {
        $this->whereHas('users', function ($query) use ($count) {
            $query->where('achievement_user.progress_count', '>=', $count);
        });

        return $this;
    }

    /**
     * Filter by completed achievements
     *
     * @return $this
     */
    public function completed(): self
    {
        $this->whereRaw('achievement_user.progress_count >= achievements.max_counts');

        return $this;
    }

    /**
     * Filter by achievements with a specific slug
     *
     * @param string $slug
     * @return $this
     */
    public function bySlug(string $slug): self
    {
        $this->where('slug', $slug);

        return $this;
    }

    /**
     * Search by name or description
     *
     * @param string $term
     * @return $this
     */
    public function search(string $term): self
    {
        $this->where(function ($query) use ($term) {
            $query->where('name', 'LIKE', "%{$term}%")
                ->orWhere('description', 'LIKE', "%{$term}%");
        });

        return $this;
    }

    /**
     * Order by max_counts
     *
     * @param string $direction
     * @return $this
     */
    public function orderByMaxCounts(string $direction = 'asc'): self
    {
        $this->orderBy('max_counts', $direction);

        return $this;
    }

    /**
     * Order by popularity (number of users who have the achievement)
     *
     * @param string $direction
     * @return $this
     */
    public function orderByPopularity(string $direction = 'desc'): self
    {
        $this->withCount('users')
            ->orderBy('users_count', $direction);

        return $this;
    }
}
