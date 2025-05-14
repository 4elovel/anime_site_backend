<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;

class DeleteComment
{
    public function __invoke(Comment $comment): void
    {
        Gate::authorize('delete', $comment);
        DB::transaction(fn () => $comment->delete());
    }
}
