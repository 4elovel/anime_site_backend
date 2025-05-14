<?php

namespace AnimeSite\Actions\CommentLikes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\CommentLike;

class DeleteCommentLike
{
    public function __invoke(CommentLike $commentLike): void
    {
        Gate::authorize('delete', $commentLike);
        DB::transaction(fn () => $commentLike->delete());
    }
}
