<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;

class UpdateComment
{
    /**
     * @param Comment $comment
     * @param array{
     *     is_spoiler?: bool,
     *     body?: string
     * } $data
     */
    public function __invoke(Comment $comment, array $data): Comment
    {
        Gate::authorize('update', $comment);

        return DB::transaction(function () use ($comment, $data) {
            $comment->update($data);
            return $comment;
        });
    }
}
