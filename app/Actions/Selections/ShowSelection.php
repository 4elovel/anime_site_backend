<?php

namespace AnimeSite\Actions\Selections;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Selection;

class ShowSelection
{
    /**
     * Отримати детальну інформацію про добірку.
     *
     * @param Selection $selection
     * @return Selection
     */
    public function __invoke(Selection $selection): Selection
    {
        Gate::authorize('view', $selection);

        return $selection->loadMissing([
            'user',
            'animes',
            'persons',
            'episodes',
            'userLists'
        ]);
    }
}
