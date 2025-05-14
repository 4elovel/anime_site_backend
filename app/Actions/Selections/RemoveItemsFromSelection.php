<?php

namespace AnimeSite\Actions\Selections;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Selection;

class RemoveItemsFromSelection
{
    /**
     * Видаляє елементи з добірки.
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
            // Видаляємо аніме з добірки
            if (isset($data['animes']) && is_array($data['animes'])) {
                $selection->animes()->detach($data['animes']);
            }

            // Видаляємо персонажів з добірки
            if (isset($data['persons']) && is_array($data['persons'])) {
                $selection->persons()->detach($data['persons']);
            }

            // Видаляємо епізоди з добірки
            if (isset($data['episodes']) && is_array($data['episodes'])) {
                $selection->episodes()->detach($data['episodes']);
            }

            return $selection->loadMissing(['user', 'animes', 'persons', 'episodes']);
        });
    }
}
