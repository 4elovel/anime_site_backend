<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;

class UnlikeComment
{
    /**
     * Скасувати лайк або дизлайк коментаря.
     *
     * @param Comment $comment
     * @return void
     */
    public function __invoke(Comment $comment): void
    {
        Gate::authorize('unlike', $comment);

        DB::transaction(function () use ($comment) {
            $userId = Auth::id();

            // Видаляємо лайк користувача для цього коментаря
            $comment->likes()
                ->where('user_id', $userId)
                ->delete();
        });
    }
}
