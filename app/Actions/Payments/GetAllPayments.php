<?php

namespace AnimeSite\Actions\Payments;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Enums\PaymentStatus;
use AnimeSite\Models\Payment;

class GetAllPayments
{
    /**
     * Отримати всі платежі з пагінацією та фільтрацією.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        Gate::authorize('viewAny', Payment::class);

        $perPage = (int) $request->input('per_page', 15);

        return Payment::query()
            ->when($request->filled('user_id'), fn($q) =>
                $q->where('user_id', $request->input('user_id'))
            )
            ->when($request->filled('tariff_id'), fn($q) =>
                $q->where('tariff_id', $request->input('tariff_id'))
            )
            ->when($request->filled('status'), fn($q) =>
                $q->where('status', $request->input('status'))
            )
            ->when($request->filled('payment_method'), fn($q) =>
                $q->where('payment_method', $request->input('payment_method'))
            )
            ->when($request->filled('transaction_id'), fn($q) =>
                $q->where('transaction_id', 'like', '%' . $request->input('transaction_id') . '%')
            )
            ->when($request->filled('min_amount') && $request->filled('max_amount'), fn($q) =>
                $q->whereBetween('amount', [
                    $request->input('min_amount'),
                    $request->input('max_amount')
                ])
            )
            ->when($request->filled('currency'), fn($q) =>
                $q->where('currency', $request->input('currency'))
            )
            ->when($request->filled('date_from'), fn($q) =>
                $q->whereDate('created_at', '>=', $request->input('date_from'))
            )
            ->when($request->filled('date_to'), fn($q) =>
                $q->whereDate('created_at', '<=', $request->input('date_to'))
            )
            ->when($request->filled('sort'), function ($query) use ($request) {
                $sort = $request->input('sort');
                $direction = 'asc';
                
                if (str_starts_with($sort, '-')) {
                    $direction = 'desc';
                    $sort = substr($sort, 1);
                }
                
                if (in_array($sort, ['created_at', 'amount', 'status'])) {
                    $query->orderBy($sort, $direction);
                }
            })
            ->with(['user', 'tariff'])
            ->paginate($perPage);
    }
}
