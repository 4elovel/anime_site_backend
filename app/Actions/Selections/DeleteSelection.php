<?php

namespace AnimeSite\Actions\Selections;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Selection;

class DeleteSelection
{
    /**
     * Видаляє запис вибору.
     *
     * @param Selection $selection
     * @return void
     */
    public function __invoke(Selection $selection): void
    {
        Gate::authorize('delete', $selection);

        DB::transaction(fn () => $selection->delete());
    }
}
