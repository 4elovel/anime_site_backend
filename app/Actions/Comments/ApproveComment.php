<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;

class ApproveComment
{
    /**
     * Затвердити коментар після скарги.
     *
     * @param Comment $comment
     * @return Comment
     */
    public function __invoke(Comment $comment): Comment
    {
        Gate::authorize('approve', $comment);

        return DB::transaction(function () use ($comment) {
            // Позначаємо всі скарги як переглянуті
            $comment->reports()->update([
                'is_viewed' => true,
            ]);
            
            // Затверджуємо коментар
            $comment->update([
                'is_approved' => true,
            ]);
            
            return $comment->load(['user', 'reports', 'reports.user'])
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
