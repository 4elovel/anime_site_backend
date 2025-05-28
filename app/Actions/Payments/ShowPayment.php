<?php

namespace AnimeSite\Actions\Payments;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Payment;

class ShowPayment
{
    /**
     * Отримати конкретний платіж.
     *
     * @param Payment $payment
     * @return Payment
     */
    public function __invoke(Payment $payment): Payment
    {
        Gate::authorize('view', $payment);
        
        return $payment->load(['user', 'tariff']);
    }
}
