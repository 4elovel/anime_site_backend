<?php

namespace AnimeSite\Actions\Achievements;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Achievement;

class CreateAchievement
{
    /**
     * @param array{
     *     slug: string,
     *     name: string,
     *     description: string,
     *     icon?: string|null,
     *     max_counts: int
     * } $data
     */
    public function __invoke(array $data): Achievement
    {
        Gate::authorize('create', Achievement::class);

        return DB::transaction(fn () =>
        Achievement::create($data)
        );
    }
}
