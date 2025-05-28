<?php

namespace AnimeSite\Actions\UserSubscriptions;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use AnimeSite\Models\UserSubscription;

class GetActiveSubscriptions
{
    /**
     * Отримати активні підписки поточного користувача.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->input('per_page', 15);
        $userId = Auth::id();

        return UserSubscription::query()
            // Тільки для поточного користувача
            ->where('user_id', $userId)
            // Тільки активні підписки
            ->where('is_active', true)
            // Тільки дійсні підписки (не прострочені)
            ->where('end_date', '>=', now())
            // Фільтрація за тарифом
            ->when($request->filled('tariff_id'), fn($q) =>
                $q->where('tariff_id', $request->input('tariff_id'))
            )
            // Фільтрація за автоматичним продовженням
            ->when($request->filled('auto_renew'), fn($q) =>
                $q->where('auto_renew', filter_var($request->input('auto_renew'), FILTER_VALIDATE_BOOLEAN))
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
                $q->orderBy('end_date', 'asc') // За замовчуванням сортуємо за датою закінчення (найближчі до закінчення спочатку)
            )
            // Завантажуємо зв'язані дані
            ->with(['tariff'])
            // Пагінація
            ->paginate($perPage);
    }
}
