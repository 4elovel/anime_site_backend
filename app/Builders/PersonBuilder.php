<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use AnimeSite\Enums\Gender;
use AnimeSite\Enums\PersonType;

class PersonBuilder extends Builder
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
     * Filter by person type
     *
     * @param PersonType $type
     * @return $this
     */
    public function byType(PersonType $type): self
    {
        $this->where('type', $type->value);

        return $this;
    }

    /**
     * Filter by person name
     *
     * @param string $name
     * @return $this
     */
    public function byName(string $name): self
    {
        $this->where('name', 'like', '%'.$name.'%')
            ->orWhere('original_name', 'like', '%'.$name.'%');

        return $this;
    }

    /**
     * Filter by person gender
     *
     * @param Gender $gender
     * @return $this
     */
    public function byGender(Gender $gender): self
    {
        $this->where('gender', $gender->value);

        return $this;
    }

    /**
     * Filter by birthplace
     *
     * @param string $birthplace
     * @return $this
     */
    public function fromBirthplace(string $birthplace): self
    {
        $this->where('birthplace', 'like', '%'.$birthplace.'%');

        return $this;
    }

    /**
     * Filter by birth year
     *
     * @param int $year
     * @return $this
     */
    public function bornInYear(int $year): self
    {
        $this->whereYear('birthday', $year);

        return $this;
    }

    /**
     * Filter by age range
     *
     * @param int $minAge
     * @param int $maxAge
     * @return $this
     */
    public function withAgeRange(int $minAge, int $maxAge): self
    {
        $minDate = now()->subYears($maxAge)->format('Y-m-d');
        $maxDate = now()->subYears($minAge)->format('Y-m-d');

        $this->whereBetween('birthday', [$minDate, $maxDate]);

        return $this;
    }

    /**
     * Filter by anime
     *
     * @param string $animeId
     * @return $this
     */
    public function inAnime(string $animeId): self
    {
        $this->whereHas('animes', function ($query) use ($animeId) {
            $query->where('animes.id', $animeId);
        });

        return $this;
    }

    /**
     * Filter by character name
     *
     * @param string $characterName
     * @return $this
     */
    public function playedCharacter(string $characterName): self
    {
        $this->whereHas('animes', function ($query) use ($characterName) {
            $query->where('anime_person.character_name', 'like', '%'.$characterName.'%');
        });

        return $this;
    }

    /**
     * Filter by voice actor
     *
     * @param string $voicePersonId
     * @return $this
     */
    public function voicedBy(string $voicePersonId): self
    {
        $this->whereHas('animes', function ($query) use ($voicePersonId) {
            $query->where('anime_person.voice_person_id', $voicePersonId);
        });

        return $this;
    }

    /**
     * Filter by selection
     *
     * @param string $selectionId
     * @return $this
     */
    public function inSelection(string $selectionId): self
    {
        $this->whereHas('selections', function ($query) use ($selectionId) {
            $query->where('selections.id', $selectionId);
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
            ->addSelect(DB::raw("ts_headline('ukrainian', original_name, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS original_name_highlight"))
            ->addSelect(DB::raw("ts_headline('ukrainian', description, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS description_highlight"))
            ->addSelect(DB::raw('similarity(name, ?) AS similarity'))
            ->whereRaw("searchable @@ websearch_to_tsquery('ukrainian', ?)", [$search, $search, $search, $search, $search, $search])
            ->orWhereRaw('name % ?', [$search])
            ->orderByDesc('rank')
            ->orderByDesc('similarity');

        return $this;
    }

    /**
     * Order by popularity (based on number of animes)
     *
     * @return $this
     */
    public function orderByPopularity(): self
    {
        $this->withCount('animes')
            ->orderByDesc('animes_count');

        return $this;
    }

    /**
     * Filter by popular people
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
     * Filter by recently added people
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
