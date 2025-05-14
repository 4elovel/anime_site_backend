<?php

namespace AnimeSite\Actions\CommentReports;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\CommentReport;

class GetAllCommentReports
{
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', CommentReport::class);

        $perPage = (int) $request->input('per_page', 15);

        return CommentReport::query()
            ->when($request->filled('comment_id'), fn($q) =>
            $q->where('comment_id', $request->input('comment_id'))
            )
            ->when($request->filled('user_id'), fn($q) =>
            $q->where('user_id', $request->input('user_id'))
            )
            ->when($request->has('is_viewed'), fn($q) =>
            $q->where('is_viewed', $request->input('is_viewed'))
            )
            ->when($request->filled('type'), fn($q) =>
            $q->where('type', $request->input('type'))
            )
            ->paginate($perPage);
    }
}
