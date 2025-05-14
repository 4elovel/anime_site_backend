<?php

namespace AnimeSite\Actions\CommentLikes;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\CommentLike;

class GetAllCommentLikes
{
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', CommentLike::class);

        $perPage = (int) $request->input('per_page', 15);

        return CommentLike::query()
            ->when($request->filled('comment_id'), fn($q) =>
            $q->where('comment_id', $request->input('comment_id'))
            )
            ->when($request->filled('user_id'), fn($q) =>
            $q->where('user_id', $request->input('user_id'))
            )
            ->paginate($perPage);
    }
}
