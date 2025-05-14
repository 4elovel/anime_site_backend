<?php

namespace AnimeSite\Actions\CommentLikes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\CommentLike;

class CreateCommentLike
{
    /**
     * @param array{
     *     comment_id: string,
     *     user_id: string,
     *     is_liked: bool
     * } $data
     */
    public function __invoke(array $data): CommentLike
    {
        Gate::authorize('create', CommentLike::class);

        return DB::transaction(fn () =>
        CommentLike::create($data)
        );
    }
}
