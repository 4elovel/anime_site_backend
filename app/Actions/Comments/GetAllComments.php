<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;

class GetAllComments
{
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Comment::class);

        $perPage = (int) $request->input('per_page', 15);

        return Comment::query()
            ->when($request->filled('commentable_type'), fn($q) =>
            $q->where('commentable_type', $request->input('commentable_type'))
            )
            ->when($request->filled('commentable_id'), fn($q) =>
            $q->where('commentable_id', $request->input('commentable_id'))
            )
            ->when($request->filled('is_spoiler'), fn($q) =>
            $q->where('is_spoiler', $request->input('is_spoiler'))
            )
            ->paginate($perPage);
    }
}
