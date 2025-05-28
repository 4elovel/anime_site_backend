<?php

namespace AnimeSite\Actions\Ratings;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Rating;

class ShowRating
{
    public function __invoke(Rating $rating): Rating
    {
        Gate::authorize('view', $rating);
        return $rating;
    }
}
