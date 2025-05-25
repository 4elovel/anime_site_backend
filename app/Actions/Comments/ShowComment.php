<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;

class ShowComment
{
    /**
     * Отримати конкретний коментар.
     *
     * @param Comment $comment
     * @return Comment
     */
    public function __invoke(Comment $comment): Comment
    {
        Gate::authorize('view', $comment);

        return $comment->load(['user', 'parent'])
            ->loadCount([
                'likes as likes_count' => function ($query) {
                    $query->where('is_liked', true);
                },
                'likes as dislikes_count' => function ($query) {
                    $query->where('is_liked', false);
                }
            ]);
    }
}
