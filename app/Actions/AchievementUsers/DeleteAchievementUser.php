<?php

namespace AnimeSite\Actions\AchievementUsers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\AchievementUser;

class DeleteAchievementUser
{
    public function __invoke(AchievementUser $achievementUser): void
    {
        Gate::authorize('delete', $achievementUser);
        DB::transaction(fn () => $achievementUser->delete());
    }
}
