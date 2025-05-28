<?php

namespace AnimeSite\Actions\CommentReports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\CommentReport;

class DeleteCommentReport
{
    public function __invoke(CommentReport $commentReport): void
    {
        Gate::authorize('delete', $commentReport);
        DB::transaction(fn () => $commentReport->delete());
    }
}
