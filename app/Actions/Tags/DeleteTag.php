<?php

namespace AnimeSite\Actions\Tags;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Tag;

class DeleteTag
{
    public function __invoke(Tag $tag): void
    {
        Gate::authorize('delete', $tag);
        DB::transaction(fn () => $tag->delete());
    }
}
