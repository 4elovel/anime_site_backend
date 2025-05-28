<?php

namespace AnimeSite\Actions\Comments;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Comment;

class GetAllComments
{
    /**
     * Отримати всі коментарі з пагінацією та фільтрацією.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
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
            ->when($request->filled('user_id'), fn($q) =>
                $q->where('user_id', $request->input('user_id'))
            )
            ->when($request->filled('is_spoiler'), fn($q) =>
                $q->where('is_spoiler', $request->input('is_spoiler'))
            )
            ->when($request->filled('is_approved'), fn($q) =>
                $q->where('is_approved', $request->input('is_approved'))
            )
            ->when($request->filled('parent_id'), fn($q) =>
                $q->where('parent_id', $request->input('parent_id'))
            )
            ->when($request->filled('search'), fn($q) =>
                $q->where('body', 'like', '%' . $request->input('search') . '%')
            )
            ->when($request->filled('sort'), function ($query) use ($request) {
                $sort = $request->input('sort');
                $direction = 'asc';

                if (str_starts_with($sort, '-')) {
                    $direction = 'desc';
                    $sort = substr($sort, 1);
                }

                if (in_array($sort, ['created_at', 'updated_at', 'is_approved'])) {
                    $query->orderBy($sort, $direction);
                }
            })
            ->with(['user', 'parent'])
            ->withCount(['likes as likes_count' => function ($query) {
                $query->where('is_liked', true);
            }])
            ->withCount(['likes as dislikes_count' => function ($query) {
                $query->where('is_liked', false);
            }])
            ->paginate($perPage);
    }
}
