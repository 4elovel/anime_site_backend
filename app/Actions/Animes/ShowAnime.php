<?php

namespace AnimeSite\Actions\Animes;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Anime;

class ShowAnime
{
    public function __invoke(Anime $anime): Anime
    {
        Gate::authorize('view', $anime);
        return $anime;
    }
}
