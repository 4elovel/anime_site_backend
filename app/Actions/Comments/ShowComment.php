<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;

class ShowComment
{
    public function __invoke(Comment $comment): Comment
    {
        Gate::authorize('view', $comment);
        return $comment;
    }
}
