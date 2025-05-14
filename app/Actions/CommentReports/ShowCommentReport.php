<?php

namespace AnimeSite\Actions\CommentReports;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\CommentReport;

class ShowCommentReport
{
    public function __invoke(CommentReport $commentReport): CommentReport
    {
        Gate::authorize('view', $commentReport);
        return $commentReport;
    }
}
