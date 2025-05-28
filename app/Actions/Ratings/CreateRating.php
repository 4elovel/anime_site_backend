<?php

namespace AnimeSite\Actions\Ratings;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Rating;

class CreateRating
{
    /**
     * @param array{
     *     user_id: string,
     *     anime_id: string,
     *     number: int,
     *     review: string|null
     * } $data
     */
    public function __invoke(array $data): Rating
    {
        Gate::authorize('create', Rating::class);

        return DB::transaction(fn () =>
        Rating::create($data)
        );
    }
}
