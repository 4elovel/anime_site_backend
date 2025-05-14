<?php

namespace AnimeSite\Actions\Episodes;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Episode;

class ShowEpisode
{
    public function __invoke(Episode $episode): Episode
    {
        Gate::authorize('view', $episode);
        return $episode;
    }
}
