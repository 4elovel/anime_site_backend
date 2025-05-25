<?php

namespace AnimeSite\Actions\Payments;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Enums\PaymentStatus;
use AnimeSite\Models\Payment;
use AnimeSite\Services\LiqPayService;

class CancelPayment
{
    /**
     * Скасувати платіж.
     *
     * @param Payment $payment
     * @return Payment
     */
    public function __invoke(Payment $payment): Payment
    {
        Gate::authorize('cancel', $payment);
        
        return DB::transaction(function () use ($payment) {
            // Можна скасувати тільки платежі в очікуванні
            if (!$payment->isPending()) {
                throw new \Exception('Можна скасувати тільки платежі в очікуванні.');
            }
            
            // Якщо метод оплати - LiqPay, скасовуємо платіж через API
            if ($payment->payment_method === 'LiqPay') {
                $liqpayService = app(LiqPayService::class);
                
                // Скасовуємо платіж через LiqPay
                $result = $liqpayService->cancelPayment($payment->transaction_id);
                
                // Оновлюємо дані LiqPay
                $payment->update([
                    'liqpay_data' => array_merge($payment->liqpay_data ?? [], [
                        'cancel_data' => $result,
                    ]),
                ]);
            }
            
            // Оновлюємо статус платежу
            $payment->update([
                'status' => PaymentStatus::FAILED,
            ]);
            
            return $payment->load(['user', 'tariff']);
        });
    }
}
