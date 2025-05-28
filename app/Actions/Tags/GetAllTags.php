<?php

namespace AnimeSite\Actions\Tags;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Tag;

class GetAllTags
{
    /**
     * Отримати список тегів з фільтрацією та пагінацією.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        // Gate::authorize('viewAny', Tag::class); // Дозволяємо перегляд тегів без авторизації

        $perPage = (int) $request->input('per_page', 15);

        return Tag::query()
            ->when($request->filled('is_genre'), fn($q) =>
            $q->where('is_genre', filter_var($request->input('is_genre'), FILTER_VALIDATE_BOOLEAN))
            )
            ->when($request->filled('search'), fn($q) =>
            $q->search($request->input('search'))
            )
            ->with(['parent', 'children'])
            ->paginate($perPage);
    }
}
