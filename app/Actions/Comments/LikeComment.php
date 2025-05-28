<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;
use AnimeSite\Models\CommentLike;

class LikeComment
{
    /**
     * Поставити лайк або дизлайк коментарю.
     *
     * @param Comment $comment
     * @param array{is_liked: bool} $data
     * @return CommentLike
     */
    public function __invoke(Comment $comment, array $data): CommentLike
    {
        Gate::authorize('like', $comment);

        return DB::transaction(function () use ($comment, $data) {
            $userId = Auth::id();

            // Перевіряємо, чи вже є лайк від цього користувача
            $existingLike = $comment->likes()
                ->where('user_id', $userId)
                ->first();

            if ($existingLike) {
                // Якщо лайк вже є, оновлюємо його
                $existingLike->update([
                    'is_liked' => $data['is_liked'],
                ]);
                
                return $existingLike;
            }

            // Якщо лайка немає, створюємо новий
            return CommentLike::create([
                'comment_id' => $comment->id,
                'user_id' => $userId,
                'is_liked' => $data['is_liked'],
            ]);
        });
    }
}
