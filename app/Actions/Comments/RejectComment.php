<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;

class RejectComment
{
    /**
     * Відхилити коментар після скарги.
     *
     * @param Comment $comment
     * @return void
     */
    public function __invoke(Comment $comment): void
    {
        Gate::authorize('reject', $comment);

        DB::transaction(function () use ($comment) {
            // Позначаємо всі скарги як переглянуті
            $comment->reports()->update([
                'is_viewed' => true,
            ]);
            
            // Видаляємо всі лайки коментаря
            $comment->likes()->delete();
            
            // Видаляємо коментар
            $comment->delete();
        });
    }
}
