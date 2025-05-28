<?php

namespace AnimeSite\Actions\CommentLikes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\CommentLike;

class UpdateCommentLike
{
    /**
     * @param CommentLike $commentLike
     * @param array{
     *     is_liked?: bool
     * } $data
     */
    public function __invoke(CommentLike $commentLike, array $data): CommentLike
    {
        Gate::authorize('update', $commentLike);

        return DB::transaction(function () use ($commentLike, $data) {
            $commentLike->update($data);
            return $commentLike;
        });
    }
}
