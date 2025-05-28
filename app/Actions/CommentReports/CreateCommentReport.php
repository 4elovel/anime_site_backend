<?php

namespace AnimeSite\Actions\CommentReports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\CommentReport;

class CreateCommentReport
{
    /**
     * @param array{
     *     comment_id: string,
     *     user_id: string,
     *     body: string|null,
     *     is_viewed: bool,
     *     type: string
     * } $data
     */
    public function __invoke(array $data): CommentReport
    {
        Gate::authorize('create', CommentReport::class);

        return DB::transaction(fn () =>
        CommentReport::create($data)
        );
    }
}
