<?php

namespace AnimeSite\Actions\CommentLikes;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\CommentLike;

class ShowCommentLike
{
    public function __invoke(CommentLike $commentLike): CommentLike
    {
        Gate::authorize('view', $commentLike);
        return $commentLike;
    }
}
