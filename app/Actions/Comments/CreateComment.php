<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;

class CreateComment
{
    /**
     * Створити новий коментар.
     *
     * @param array{
     *     commentable_type: string,
     *     commentable_id: string,
     *     user_id: string,
     *     is_spoiler: bool,
     *     body: string,
     *     parent_id?: string|null
     * } $data
     * @return Comment
     */
    public function __invoke(array $data): Comment
    {
        Gate::authorize('create', Comment::class);

        return DB::transaction(function () use ($data) {
            // Створюємо коментар
            $comment = Comment::create([
                'commentable_type' => $data['commentable_type'],
                'commentable_id' => $data['commentable_id'],
                'user_id' => $data['user_id'],
                'is_spoiler' => $data['is_spoiler'] ?? false,
                'body' => $data['body'],
                'parent_id' => $data['parent_id'] ?? null,
                'is_approved' => true, // За замовчуванням коментар затверджений
            ]);

            return $comment->load(['user']);
        });
    }
}
