<?php

namespace AnimeSite\Actions\Animes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Anime;

class DeleteAnime
{
    public function __invoke(Anime $anime): void
    {
        Gate::authorize('delete', $anime);
        DB::transaction(fn () => $anime->delete());
    }
}
