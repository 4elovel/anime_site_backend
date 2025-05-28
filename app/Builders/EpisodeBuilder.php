<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class EpisodeBuilder extends Builder
{
    /**
     * Search by a term in specified columns
     *
     * @param string $term
     * @param array $columns
     * @return $this
     */
    public function search(string $term, array $columns = ['name']): self
    {
        $this->where(function ($query) use ($term, $columns) {
            foreach ($columns as $column) {
                $query->orWhere($column, 'LIKE', "%{$term}%");
            }
        });

        return $this;
    }

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
     * Filter by active status
     *
     * @param bool $active
     * @return $this
     */
    public function active(bool $active = true): self
    {
        $this->where('is_active', $active);

        return $this;
    }

    /**
     * Filter by published status
     *
     * @param bool $published
     * @return $this
     */
    public function published(bool $published = true): self
    {
        $this->where('is_published', $published);

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
     * Filter by episode number
     *
     * @param int $number
     * @return $this
     */
    public function withNumber(int $number): self
    {
        $this->where('number', $number);

        return $this;
    }

    /**
     * Filter by air date after a specific date
     *
     * @param Carbon $date
     * @return $this
     */
    public function airedAfter(Carbon $date): self
    {
        $this->where('air_date', '>=', $date);

        return $this;
    }

    /**
     * Filter by air date before a specific date
     *
     * @param Carbon $date
     * @return $this
     */
    public function airedBefore(Carbon $date): self
    {
        $this->where('air_date', '<=', $date);

        return $this;
    }

    /**
     * Filter by air date between two dates
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return $this
     */
    public function airedBetween(Carbon $startDate, Carbon $endDate): self
    {
        $this->whereBetween('air_date', [$startDate, $endDate]);

        return $this;
    }

    /**
     * Filter by duration
     *
     * @param int $minutes
     * @param string $operator
     * @return $this
     */
    public function withDuration(int $minutes, string $operator = '='): self
    {
        $this->where('duration', $operator, $minutes);

        return $this;
    }

    /**
     * Filter by filler episodes
     *
     * @param bool $isFiller
     * @return $this
     */
    public function filler(bool $isFiller = true): self
    {
        $this->where('is_filler', $isFiller);

        return $this;
    }

    /**
     * Filter by episodes with pictures
     *
     * @return $this
     */
    public function withPictures(): self
    {
        $this->whereNotNull('pictures')
            ->where('pictures', '!=', '[]');

        return $this;
    }

    /**
     * Filter by episodes with video players
     *
     * @return $this
     */
    public function withVideoPlayers(): self
    {
        $this->whereNotNull('video_players')
            ->where('video_players', '!=', '[]');

        return $this;
    }

    /**
     * Filter by episodes that are airing today
     *
     * @return $this
     */
    public function airingToday(): self
    {
        $this->whereDate('air_date', Carbon::today());

        return $this;
    }

    /**
     * Filter by episodes that are airing this week
     *
     * @return $this
     */
    public function airingThisWeek(): self
    {
        $this->whereBetween('air_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

        return $this;
    }

    /**
     * Filter by episodes that are airing this month
     *
     * @return $this
     */
    public function airingThisMonth(): self
    {
        $this->whereBetween('air_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);

        return $this;
    }

    /**
     * Order by episode number
     *
     * @param string $direction
     * @return $this
     */
    public function orderByNumber(string $direction = 'asc'): self
    {
        $this->orderBy('number', $direction);

        return $this;
    }

    /**
     * Order by air date
     *
     * @param string $direction
     * @return $this
     */
    public function orderByAirDate(string $direction = 'asc'): self
    {
        $this->orderBy('air_date', $direction);

        return $this;
    }

    /**
     * Filter by episodes with comments
     *
     * @return $this
     */
    public function withComments(): self
    {
        $this->has('comments');

        return $this;
    }

    /**
     * Filter by episodes in selections
     *
     * @return $this
     */
    public function inSelections(): self
    {
        $this->has('selections');

        return $this;
    }
}
