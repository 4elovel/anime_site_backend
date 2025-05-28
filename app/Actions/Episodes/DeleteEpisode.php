<?php

namespace AnimeSite\Actions\Episodes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Episode;

class DeleteEpisode
{
    public function __invoke(Episode $episode): void
    {
        Gate::authorize('delete', $episode);
        DB::transaction(fn () => $episode->delete());
    }
}
