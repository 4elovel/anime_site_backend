<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TagBuilder extends Builder
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
     * Filter by genre tags
     *
     * @param bool $isGenre
     * @return $this
     */
    public function genres(bool $isGenre = true): self
    {
        $this->where('is_genre', $isGenre);

        return $this;
    }

    /**
     * Filter by tags used in a specific anime
     *
     * @param string $animeId
     * @return $this
     */
    public function usedInAnime(string $animeId): self
    {
        $this->whereHas('animes', function ($query) use ($animeId) {
            $query->where('animes.id', $animeId);
        });

        return $this;
    }

    /**
     * Filter by popular tags
     *
     * @param int $minAnimes
     * @return $this
     */
    public function popular(int $minAnimes = 3): self
    {
        $this->withCount('animes')
            ->having('animes_count', '>=', $minAnimes)
            ->orderByDesc('animes_count');

        return $this;
    }

    /**
     * Order by number of animes using the tag
     *
     * @param string $direction
     * @return $this
     */
    public function orderByUsageCount(string $direction = 'desc'): self
    {
        $this->withCount('animes')
            ->orderBy('animes_count', $direction);

        return $this;
    }

    /**
     * Search by name or slug
     *
     * @param string $term
     * @return $this
     */
    public function search(string $term): self
    {
        $this->where(function ($query) use ($term) {
            $query->where('name', 'LIKE', "%{$term}%")
                ->orWhere('slug', 'LIKE', "%{$term}%");
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
            ->addSelect(DB::raw("ts_headline('ukrainian', aliases::text, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS aliases_highlight"))
            ->addSelect(DB::raw('similarity(name, ?) AS similarity'))
            ->whereRaw("searchable @@ websearch_to_tsquery('ukrainian', ?)", [$search, $search, $search, $search, $search, $search])
            ->orWhereRaw('name % ?', [$search])
            ->orderByDesc('rank')
            ->orderByDesc('similarity');

        return $this;
    }

    /**
     * Filter by tags that are favorited by users
     *
     * @return $this
     */
    public function favorited(): self
    {
        $this->has('userLists');

        return $this;
    }

    /**
     * Filter by tags favorited by a specific user
     *
     * @param string $userId
     * @return $this
     */
    public function favoritedByUser(string $userId): self
    {
        $this->whereHas('userLists', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });

        return $this;
    }

    /**
     * Filter by tags with images
     *
     * @return $this
     */
    public function withImages(): self
    {
        $this->whereNotNull('image');

        return $this;
    }

    /**
     * Filter by tags with aliases
     *
     * @return $this
     */
    public function withAliases(): self
    {
        $this->whereNotNull('aliases')
            ->where('aliases', '!=', '[]');

        return $this;
    }

    /**
     * Filter by recently created tags
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
}
