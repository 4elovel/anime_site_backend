<?php

namespace AnimeSite\Actions\Studios;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Studio;

class DeleteStudio
{
    public function __invoke(Studio $studio): void
    {
        Gate::authorize('delete', $studio);
        DB::transaction(fn () => $studio->delete());
    }
}
