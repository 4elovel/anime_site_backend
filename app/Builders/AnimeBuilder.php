<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use AnimeSite\Enums\Country;
use AnimeSite\Enums\Kind;
use AnimeSite\Enums\Period;
use AnimeSite\Enums\RestrictedRating;
use AnimeSite\Enums\Source;
use AnimeSite\Enums\Status;
use AnimeSite\Enums\VideoQuality;

class AnimeBuilder extends Builder
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
     * Filter by anime kind
     *
     * @param Kind $kind
     * @return $this
     */
    public function ofKind(Kind $kind): self
    {
        $this->where('kind', $kind->value);

        return $this;
    }

    /**
     * Filter by anime status
     *
     * @param Status $status
     * @return $this
     */
    public function withStatus(Status $status): self
    {
        $this->where('status', $status->value);

        return $this;
    }

    /**
     * Filter by anime period
     *
     * @param Period $period
     * @return $this
     */
    public function ofPeriod(Period $period): self
    {
        $this->where('period', $period->value);

        return $this;
    }

    /**
     * Filter by restricted rating
     *
     * @param RestrictedRating $restrictedRating
     * @return $this
     */
    public function withRestrictedRating(RestrictedRating $restrictedRating): self
    {
        $this->where('restricted_rating', $restrictedRating->value);

        return $this;
    }

    /**
     * Filter by source
     *
     * @param Source $source
     * @return $this
     */
    public function fromSource(Source $source): self
    {
        $this->where('source', $source->value);

        return $this;
    }

    /**
     * Filter by video quality
     *
     * @param VideoQuality $videoQuality
     * @return $this
     */
    public function withVideoQuality(VideoQuality $videoQuality): self
    {
        $this->where('video_quality', $videoQuality->value);

        return $this;
    }

    /**
     * Filter by IMDb score
     *
     * @param float $score
     * @return $this
     */
    public function withImdbScoreGreaterThan(float $score): self
    {
        $this->where('imdb_score', '>=', $score);

        return $this;
    }

    /**
     * Filter by country
     *
     * @param Country $country
     * @return $this
     */
    public function fromCountry(Country $country): self
    {
        $this->whereJsonContains('countries', $country->value);

        return $this;
    }

    /**
     * Filter by studio
     *
     * @param string $studioId
     * @return $this
     */
    public function fromStudio(string $studioId): self
    {
        $this->where('studio_id', $studioId);

        return $this;
    }

    /**
     * Filter by tag
     *
     * @param string $tagId
     * @return $this
     */
    public function withTag(string $tagId): self
    {
        $this->whereHas('tags', function ($query) use ($tagId) {
            $query->where('tags.id', $tagId);
        });

        return $this;
    }

    /**
     * Filter by person
     *
     * @param string $personId
     * @return $this
     */
    public function withPerson(string $personId): self
    {
        $this->whereHas('people', function ($query) use ($personId) {
            $query->where('people.id', $personId);
        });

        return $this;
    }

    /**
     * Filter by release year
     *
     * @param int $year
     * @return $this
     */
    public function releasedInYear(int $year): self
    {
        $this->whereYear('first_air_date', $year);

        return $this;
    }

    /**
     * Filter by episodes count
     *
     * @param int $count
     * @param string $operator
     * @return $this
     */
    public function withEpisodesCount(int $count, string $operator = '='): self
    {
        $this->where('episodes_count', $operator, $count);

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
            ->addSelect(DB::raw("ts_headline('ukrainian', aliases, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS aliases_highlight"))
            ->addSelect(DB::raw("ts_headline('ukrainian', description, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS description_highlight"))
            ->addSelect(DB::raw('similarity(name, ?) AS similarity'))
            ->whereRaw("searchable @@ websearch_to_tsquery('ukrainian', ?)", [$search, $search, $search, $search, $search, $search])
            ->orWhereRaw('name % ?', [$search])
            ->orderByDesc('rank')
            ->orderByDesc('similarity');

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
     * Filter by air date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return $this
     */
    public function airedBetween(string $startDate, string $endDate): self
    {
        $this->whereBetween('first_air_date', [$startDate, $endDate]);

        return $this;
    }

    /**
     * Filter by similar animes
     *
     * @param string $animeId
     * @return $this
     */
    public function similarTo(string $animeId): self
    {
        $this->whereJsonContains('similars', $animeId);

        return $this;
    }

    /**
     * Filter by related animes
     *
     * @param string $animeId
     * @return $this
     */
    public function relatedTo(string $animeId): self
    {
        $this->whereJsonContains('related', ['anime_id' => $animeId]);

        return $this;
    }

    /**
     * Filter by popular animes (based on ratings count)
     *
     * @param int $minRatings
     * @return $this
     */
    public function popular(int $minRatings = 10): self
    {
        $this->withCount('ratings')
            ->having('ratings_count', '>=', $minRatings)
            ->orderByDesc('imdb_score');

        return $this;
    }

    /**
     * Filter by top rated animes
     *
     * @param float $minScore
     * @return $this
     */
    public function topRated(float $minScore = 7.0): self
    {
        $this->where('imdb_score', '>=', $minScore)
            ->orderByDesc('imdb_score');

        return $this;
    }

    /**
     * Filter by recently added animes
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

    /**
     * Filter by recently updated animes
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
