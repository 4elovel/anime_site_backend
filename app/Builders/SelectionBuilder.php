<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SelectionBuilder extends Builder
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
     * Filter by selections containing a specific anime
     *
     * @param string $animeId
     * @return $this
     */
    public function containingAnime(string $animeId): self
    {
        $this->whereHas('animes', function ($query) use ($animeId) {
            $query->where('animes.id', $animeId);
        });

        return $this;
    }

    /**
     * Filter by selections containing a specific person
     *
     * @param string $personId
     * @return $this
     */
    public function containingPerson(string $personId): self
    {
        $this->whereHas('persons', function ($query) use ($personId) {
            $query->where('people.id', $personId);
        });

        return $this;
    }

    /**
     * Filter by selections containing a specific episode
     *
     * @param string $episodeId
     * @return $this
     */
    public function containingEpisode(string $episodeId): self
    {
        $this->whereHas('episodes', function ($query) use ($episodeId) {
            $query->where('episodes.id', $episodeId);
        });

        return $this;
    }

    /**
     * Filter by selections with a minimum number of items
     *
     * @param int $count
     * @return $this
     */
    public function withMinItems(int $count): self
    {
        $this->has('animes', '>=', $count)
            ->orHas('persons', '>=', $count)
            ->orHas('episodes', '>=', $count);

        return $this;
    }

    /**
     * Order by number of items
     *
     * @return $this
     */
    public function orderByItemCount(): self
    {
        $this->withCount(['animes', 'persons', 'episodes'])
            ->orderByRaw('animes_count + persons_count + episodes_count DESC');

        return $this;
    }

    /**
     * Filter by popular selections
     *
     * @return $this
     */
    public function popular(): self
    {
        $this->withCount('userLists')
            ->orderByDesc('user_lists_count');

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
     * Filter by selections created by admin users
     *
     * @return $this
     */
    public function byAdmins(): self
    {
        $this->whereHas('user', function ($query) {
            $query->where('role', 'admin');
        });

        return $this;
    }

    /**
     * Filter by recently created selections
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
     * Filter by recently updated selections
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
