<?php

namespace AnimeSite\Actions\Selections;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Selection;

class GetAllSelections
{
    /**
     * Отримати список добірок з фільтрацією, сортуванням та пагінацією.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Selection::class);

        $perPage = (int) $request->input('per_page', 15);
        $query = Selection::query();

        // Повнотекстовий пошук
        if ($request->filled('search')) {
            $query->fullTextSearch($request->input('search'));
        }

        // Фільтрація
        $query->when($request->filled('user_id'), fn($q) =>
            $q->where('user_id', $request->input('user_id'))
        )
        ->when($request->filled('is_published'), fn($q) =>
            $q->where('is_published', filter_var($request->input('is_published'), FILTER_VALIDATE_BOOLEAN))
        )
        ->when($request->filled('is_active'), fn($q) =>
            $q->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN))
        )
        ->when($request->filled('has_anime'), fn($q) =>
            $q->whereHas('animes')
        )
        ->when($request->filled('has_person'), fn($q) =>
            $q->whereHas('persons')
        )
        ->when($request->filled('has_episode'), fn($q) =>
            $q->whereHas('episodes')
        );

        // Сортування
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        // Перевірка допустимих полів для сортування
        $allowedSortFields = ['name', 'created_at', 'updated_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Завантаження зв'язків
        $query->with(['user', 'animes', 'persons', 'episodes']);

        return $query->paginate($perPage);
    }
}
