<?php

namespace AnimeSite\Actions\UserSubscriptions;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\UserSubscription;

class GetAllUserSubscriptions
{
    /**
     * Отримати всі підписки користувачів з пагінацією та фільтрацією.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', UserSubscription::class);

        $perPage = (int) $request->input('per_page', 15);

        return UserSubscription::query()
            // Фільтрація за користувачем
            ->when($request->filled('user_id'), fn($q) =>
                $q->where('user_id', $request->input('user_id'))
            )
            // Фільтрація за тарифом
            ->when($request->filled('tariff_id'), fn($q) =>
                $q->where('tariff_id', $request->input('tariff_id'))
            )
            // Фільтрація за активністю
            ->when($request->filled('is_active'), fn($q) =>
                $q->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN))
            )
            // Фільтрація за автоматичним продовженням
            ->when($request->filled('auto_renew'), fn($q) =>
                $q->where('auto_renew', filter_var($request->input('auto_renew'), FILTER_VALIDATE_BOOLEAN))
            )
            // Фільтрація за датою початку
            ->when($request->filled('start_date_from') && $request->filled('start_date_to'), fn($q) =>
                $q->whereBetween('start_date', [
                    $request->input('start_date_from'),
                    $request->input('start_date_to')
                ])
            )
            // Фільтрація за датою закінчення
            ->when($request->filled('end_date_from') && $request->filled('end_date_to'), fn($q) =>
                $q->whereBetween('end_date', [
                    $request->input('end_date_from'),
                    $request->input('end_date_to')
                ])
            )
            // Фільтрація за терміном дії (активні, прострочені)
            ->when($request->input('status') === 'expired', fn($q) =>
                $q->where('end_date', '<', now())
            )
            ->when($request->input('status') === 'active', fn($q) =>
                $q->where('end_date', '>=', now())
                  ->where('is_active', true)
            )
            // Сортування
            ->when($request->filled('sort'), function ($query) use ($request) {
                $sort = $request->input('sort');
                $direction = 'asc';
                
                if (str_starts_with($sort, '-')) {
                    $direction = 'desc';
                    $sort = substr($sort, 1);
                }
                
                if (in_array($sort, ['created_at', 'start_date', 'end_date'])) {
                    $query->orderBy($sort, $direction);
                }
            }, fn($q) =>
                $q->orderBy('created_at', 'desc') // За замовчуванням сортуємо за датою створення (нові спочатку)
            )
            // Завантажуємо зв'язані дані
            ->with(['user', 'tariff'])
            // Пагінація
            ->paginate($perPage);
    }
}
