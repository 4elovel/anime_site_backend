<?php

namespace AnimeSite\Actions\Ratings;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Rating;

class UpdateRating
{
    /**
     * @param Rating $rating
     * @param array{
     *     number?: int,
     *     review?: string|null
     * } $data
     */
    public function __invoke(Rating $rating, array $data): Rating
    {
        Gate::authorize('update', $rating);

        return DB::transaction(function () use ($rating, $data) {
            $rating->update($data);
            return $rating;
        });
    }
}
