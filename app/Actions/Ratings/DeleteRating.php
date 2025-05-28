<?php

namespace AnimeSite\Actions\Ratings;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Rating;

class DeleteRating
{
    public function __invoke(Rating $rating): void
    {
        Gate::authorize('delete', $rating);
        DB::transaction(fn () => $rating->delete());
    }
}
