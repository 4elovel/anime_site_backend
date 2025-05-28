<?php

namespace AnimeSite\Actions\Selections;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Person;
use AnimeSite\Models\Selection;

class AddItemsToSelection
{
    /**
     * Додає елементи до добірки.
     *
     * @param Selection $selection
     * @param array{
     *     animes?: array<string>,
     *     persons?: array<string>,
     *     episodes?: array<string>
     * } $data
     * @return Selection
     */
    public function __invoke(Selection $selection, array $data): Selection
    {
        Gate::authorize('update', $selection);

        return DB::transaction(function () use ($selection, $data) {
            // Додаємо аніме до добірки
            if (isset($data['animes']) && is_array($data['animes'])) {
                $animeIds = Anime::whereIn('id', $data['animes'])->pluck('id')->toArray();
                $selection->animes()->syncWithoutDetaching($animeIds);
            }

            // Додаємо персонажів до добірки
            if (isset($data['persons']) && is_array($data['persons'])) {
                $personIds = Person::whereIn('id', $data['persons'])->pluck('id')->toArray();
                $selection->persons()->syncWithoutDetaching($personIds);
            }

            // Додаємо епізоди до добірки
            if (isset($data['episodes']) && is_array($data['episodes'])) {
                $episodeIds = Episode::whereIn('id', $data['episodes'])->pluck('id')->toArray();
                $selection->episodes()->syncWithoutDetaching($episodeIds);
            }

            return $selection->loadMissing(['user', 'animes', 'persons', 'episodes']);
        });
    }
}
