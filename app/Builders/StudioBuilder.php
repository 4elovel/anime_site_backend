<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StudioBuilder extends Builder
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
     * Filter by studio name
     *
     * @param string $name
     * @return $this
     */
    public function byName(string $name): self
    {
        $this->where('name', 'like', '%'.$name.'%');

        return $this;
    }

    /**
     * Filter by studios with a minimum number of animes
     *
     * @param int $count
     * @return $this
     */
    public function withMinAnimeCount(int $count): self
    {
        $this->withCount('animes')
            ->having('animes_count', '>=', $count);

        return $this;
    }

    /**
     * Order by number of animes
     *
     * @param string $direction
     * @return $this
     */
    public function orderByAnimeCount(string $direction = 'desc'): self
    {
        $this->withCount('animes')
            ->orderBy('animes_count', $direction);

        return $this;
    }

    /**
     * Filter by studios that have produced animes of a specific kind
     *
     * @param string $kind
     * @return $this
     */
    public function producedAnimeOfKind(string $kind): self
    {
        $this->whereHas('animes', function ($query) use ($kind) {
            $query->where('kind', $kind);
        });

        return $this;
    }

    /**
     * Filter by studios that have produced animes with a minimum IMDb score
     *
     * @param float $score
     * @return $this
     */
    public function producedHighRatedAnime(float $score = 7.0): self
    {
        $this->whereHas('animes', function ($query) use ($score) {
            $query->where('imdb_score', '>=', $score);
        });

        return $this;
    }

    /**
     * Filter by studios that have produced animes in a specific year
     *
     * @param int $year
     * @return $this
     */
    public function producedAnimeInYear(int $year): self
    {
        $this->whereHas('animes', function ($query) use ($year) {
            $query->whereYear('first_air_date', $year);
        });

        return $this;
    }

    /**
     * Full-text search
     *
     * @param string $search
     * @return $this
     */
    public function fullTextSearch(string $search): self
    {
        $this->select('*')
            ->addSelect(DB::raw("ts_rank(searchable, websearch_to_tsquery('ukrainian', ?)) AS rank"))
            ->addSelect(DB::raw("ts_headline('ukrainian', name, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS name_highlight"))
            ->addSelect(DB::raw("ts_headline('ukrainian', description, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS description_highlight"))
            ->addSelect(DB::raw('similarity(name, ?) AS similarity'))
            ->whereRaw("searchable @@ websearch_to_tsquery('ukrainian', ?)", [$search, $search, $search, $search, $search])
            ->orWhereRaw('name % ?', [$search])
            ->orderByDesc('rank')
            ->orderByDesc('similarity');

        return $this;
    }

    /**
     * Filter by popular studios
     *
     * @param int $minAnimes
     * @return $this
     */
    public function popular(int $minAnimes = 5): self
    {
        $this->withCount('animes')
            ->having('animes_count', '>=', $minAnimes)
            ->orderByDesc('animes_count');

        return $this;
    }

    /**
     * Filter by recently added studios
     *
     * @param int $days
     * @return $this
     */
    public function addedInLastDays(int $days = 30): self
    {
        $this->where('created_at', '>=', now()->subDays($days))
            ->orderByDesc('created_at');

        return $this;
    }
}
