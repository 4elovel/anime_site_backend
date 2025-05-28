<?php

namespace AnimeSite\Actions\AchievementUsers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\AchievementUser;

class CreateAchievementUser
{
    /**
     * @param array{
     *     user_id: string,
     *     achievement_id: string,
     *     progress_count: int
     * } $data
     */
    public function __invoke(array $data): AchievementUser
    {
        Gate::authorize('create', AchievementUser::class);

        return DB::transaction(fn () =>
        AchievementUser::create($data)
        );
    }
}
