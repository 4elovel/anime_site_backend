<?php

namespace AnimeSite\Actions\CommentReports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\CommentReport;

class UpdateCommentReport
{
    /**
     * @param CommentReport $commentReport
     * @param array{
     *     is_viewed?: bool,
     *     body?: string,
     *     type?: string
     * } $data
     */
    public function __invoke(CommentReport $commentReport, array $data): CommentReport
    {
        Gate::authorize('update', $commentReport);

        return DB::transaction(function () use ($commentReport, $data) {
            $commentReport->update($data);
            return $commentReport;
        });
    }
}
