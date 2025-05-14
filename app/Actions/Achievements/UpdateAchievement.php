<?php

namespace AnimeSite\Actions\Achievements;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Achievement;

class UpdateAchievement
{
    /**
     * @param Achievement $achievement
     * @param array{
     *     slug?: string,
     *     name?: string,
     *     description?: string,
     *     icon?: string|null,
     *     max_counts?: int
     * } $data
     */
    public function __invoke(Achievement $achievement, array $data): Achievement
    {
        Gate::authorize('update', $achievement);

        return DB::transaction(function () use ($achievement, $data) {
            $achievement->update($data);
            return $achievement;
        });
    }
}
