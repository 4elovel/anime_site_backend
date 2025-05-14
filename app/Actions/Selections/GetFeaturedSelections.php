<?php

namespace AnimeSite\Actions\Selections;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Selection;

class GetFeaturedSelections
{
    /**
     * Отримати список рекомендованих добірок.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Selection::class);

        $perPage = (int) $request->input('per_page', 15);
        $query = Selection::query();

        // Фільтруємо тільки опубліковані та активні добірки
        $query->where('is_published', true)
              ->where('is_active', true);

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
