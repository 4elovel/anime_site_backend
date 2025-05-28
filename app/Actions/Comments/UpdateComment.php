<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;

class UpdateComment
{
    /**
     * Оновити коментар.
     *
     * @param Comment $comment
     * @param array{
     *     is_spoiler?: bool,
     *     body?: string
     * } $data
     * @return Comment
     */
    public function __invoke(Comment $comment, array $data): Comment
    {
        Gate::authorize('update', $comment);

        return DB::transaction(function () use ($comment, $data) {
            $comment->update($data);

            return $comment->load(['user', 'parent'])
                ->loadCount([
                    'likes as likes_count' => function ($query) {
                        $query->where('is_liked', true);
                    },
                    'likes as dislikes_count' => function ($query) {
                        $query->where('is_liked', false);
                    }
                ]);
        });
    }
}
