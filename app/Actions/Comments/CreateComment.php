<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;

class CreateComment
{
    /**
     * @param array{
     *     commentable_type: string,
     *     commentable_id: string,
     *     user_id: string,
     *     is_spoiler: bool,
     *     body: string
     * } $data
     */
    public function __invoke(array $data): Comment
    {
        Gate::authorize('create', Comment::class);

        return DB::transaction(fn () =>
        Comment::create($data)
        );
    }
}
