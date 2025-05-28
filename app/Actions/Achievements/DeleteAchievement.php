<?php

namespace AnimeSite\Actions\Achievements;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Achievement;

class DeleteAchievement
{
    public function __invoke(Achievement $achievement): void
    {
        Gate::authorize('delete', $achievement);
        DB::transaction(fn () => $achievement->delete());
    }
}

