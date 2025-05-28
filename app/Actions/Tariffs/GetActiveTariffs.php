<?php

namespace AnimeSite\Actions\Tariffs;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use AnimeSite\Models\Tariff;

class GetActiveTariffs
{
    /**
     * Отримати всі активні тарифи з пагінацією та фільтрацією.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        $perPage = (int) $request->input('per_page', 15);

        return Tariff::query()
            // Тільки активні тарифи
            ->where('is_active', true)
            // Фільтрація за ціною
            ->when($request->filled('min_price') && $request->filled('max_price'), fn($q) =>
                $q->whereBetween('price', [
                    $request->input('min_price'),
                    $request->input('max_price')
                ])
            )
            // Фільтрація за валютою
            ->when($request->filled('currency'), fn($q) =>
                $q->where('currency', $request->input('currency'))
            )
            // Фільтрація за тривалістю
            ->when($request->filled('min_duration') && $request->filled('max_duration'), fn($q) =>
                $q->whereBetween('duration_days', [
                    $request->input('min_duration'),
                    $request->input('max_duration')
                ])
            )
            // Фільтрація за функціями
            ->when($request->filled('features'), fn($q) =>
                $q->whereJsonContains('features', $request->input('features'))
            )
            // Пошук за назвою або описом
            ->when($request->filled('search'), fn($q) =>
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->input('search') . '%')
                        ->orWhere('description', 'like', '%' . $request->input('search') . '%');
                })
            )
            // Сортування
            ->when($request->filled('sort'), function ($query) use ($request) {
                $sort = $request->input('sort');
                $direction = 'asc';
                
                if (str_starts_with($sort, '-')) {
                    $direction = 'desc';
                    $sort = substr($sort, 1);
                }
                
                if (in_array($sort, ['name', 'price', 'duration_days', 'created_at'])) {
                    $query->orderBy($sort, $direction);
                }
            }, fn($q) =>
                $q->orderBy('price', 'asc') // За замовчуванням сортуємо за ціною (від найдешевшого)
            )
            // Пагінація
            ->paginate($perPage);
    }
}
