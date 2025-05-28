<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;
use AnimeSite\Models\CommentReport;

class ReportComment
{
    /**
     * Поскаржитися на коментар.
     *
     * @param Comment $comment
     * @param array{type: string, body?: string|null} $data
     * @return CommentReport
     */
    public function __invoke(Comment $comment, array $data): CommentReport
    {
        Gate::authorize('report', $comment);

        return DB::transaction(function () use ($comment, $data) {
            $userId = Auth::id();

            // Перевіряємо, чи вже є скарга від цього користувача
            $existingReport = $comment->reports()
                ->where('user_id', $userId)
                ->first();

            if ($existingReport) {
                // Якщо скарга вже є, оновлюємо її
                $existingReport->update([
                    'type' => $data['type'],
                    'body' => $data['body'] ?? $existingReport->body,
                    'is_viewed' => false, // Скидаємо статус перегляду
                ]);
                
                return $existingReport;
            }

            // Якщо скарги немає, створюємо нову
            return CommentReport::create([
                'comment_id' => $comment->id,
                'user_id' => $userId,
                'type' => $data['type'],
                'body' => $data['body'] ?? null,
                'is_viewed' => false,
            ]);
        });
    }
}
