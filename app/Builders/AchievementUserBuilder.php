<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;

class AchievementUserBuilder extends Builder
{
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
     * Filter by achievement
     *
     * @param string $achievementId
     * @return $this
     */
    public function byAchievement(string $achievementId): self
    {
        $this->where('achievement_id', $achievementId);

        return $this;
    }

    /**
     * Filter by minimum progress count
     *
     * @param int $count
     * @return $this
     */
    public function withMinProgressCount(int $count): self
    {
        $this->where('progress_count', '>=', $count);

        return $this;
    }

    /**
     * Filter by maximum progress count
     *
     * @param int $count
     * @return $this
     */
    public function withMaxProgressCount(int $count): self
    {
        $this->where('progress_count', '<=', $count);

        return $this;
    }

    /**
     * Filter by progress count range
     *
     * @param int $min
     * @param int $max
     * @return $this
     */
    public function withProgressCountRange(int $min, int $max): self
    {
        $this->whereBetween('progress_count', [$min, $max]);

        return $this;
    }

    /**
     * Filter by completed achievements
     *
     * @return $this
     */
    public function completed(): self
    {
        $this->whereHas('achievement', function ($query) {
            $query->whereRaw('achievement_user.progress_count >= achievements.max_counts');
        });

        return $this;
    }

    /**
     * Order by progress count
     *
     * @param string $direction
     * @return $this
     */
    public function orderByProgressCount(string $direction = 'desc'): self
    {
        $this->orderBy('progress_count', $direction);

        return $this;
    }
}
