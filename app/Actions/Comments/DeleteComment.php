<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;

class DeleteComment
{
    /**
     * Видалити коментар.
     *
     * @param Comment $comment
     * @return void
     */
    public function __invoke(Comment $comment): void
    {
        Gate::authorize('delete', $comment);

        DB::transaction(function () use ($comment) {
            // Видаляємо всі лайки та репорти коментаря
            $comment->likes()->delete();
            $comment->reports()->delete();

            // Видаляємо сам коментар
            $comment->delete();
        });
    }
}
