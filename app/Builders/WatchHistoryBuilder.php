<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;

class WatchHistoryBuilder extends Builder
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
     * Filter by episode
     *
     * @param string $episodeId
     * @return $this
     */
    public function forEpisode(string $episodeId): self
    {
        $this->where('episode_id', $episodeId);

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
        $this->whereHas('episode', function ($query) use ($animeId) {
            $query->where('anime_id', $animeId);
        });

        return $this;
    }

    /**
     * Filter by completed watches
     *
     * @return $this
     */
    public function completed(): self
    {
        $this->where('is_completed', true);

        return $this;
    }

    /**
     * Filter by incomplete watches
     *
     * @return $this
     */
    public function incomplete(): self
    {
        $this->where('is_completed', false);
    }

    /**
     * Filter by watch progress
     *
     * @param float $minProgress
     * @return $this
     */
    public function withMinProgress(float $minProgress): self
    {
        $this->where('progress', '>=', $minProgress);

        return $this;
    }

    /**
     * Filter by recently watched
     *
     * @param int $days
     * @return $this
     */
    public function watchedInLastDays(int $days = 7): self
    {
        $this->where('created_at', '>=', now()->subDays($days))
            ->orderByDesc('created_at');

        return $this;
    }

    /**
     * Order by watch date
     *
     * @param string $direction
     * @return $this
     */
    public function orderByWatchDate(string $direction = 'desc'): self
    {
        $this->orderBy('created_at', $direction);

        return $this;
    }

    /**
     * Order by progress
     *
     * @param string $direction
     * @return $this
     */
    public function orderByProgress(string $direction = 'desc'): self
    {
        $this->orderBy('progress', $direction);

        return $this;
    }

    /**
     * Filter by watches with a specific duration
     *
     * @param int $minSeconds
     * @return $this
     */
    public function withMinDuration(int $minSeconds): self
    {
        $this->where('duration', '>=', $minSeconds);

        return $this;
    }

    /**
     * Clean old history for a user
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
