<?php

namespace AnimeSite\Actions\Payments;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use AnimeSite\Enums\PaymentStatus;
use AnimeSite\Models\Payment;
use AnimeSite\Models\UserSubscription;
use AnimeSite\Services\LiqPayService;

class RefundPayment
{
    /**
     * Повернути кошти за платіж.
     *
     * @param Payment $payment
     * @param array{
     *     amount?: float|null,
     *     comment?: string|null
     * } $data
     * @return Payment
     */
    public function __invoke(Payment $payment, array $data = []): Payment
    {
        Gate::authorize('refund', $payment);
        
        return DB::transaction(function () use ($payment, $data) {
            // Можна повернути кошти тільки за успішні платежі
            if (!$payment->isSuccessful()) {
                throw new \Exception('Можна повернути кошти тільки за успішні платежі.');
            }
            
            // Сума повернення (за замовчуванням - повна сума платежу)
            $amount = $data['amount'] ?? $payment->amount;
            
            // Коментар до повернення
            $comment = $data['comment'] ?? 'Повернення коштів';
            
            // Якщо метод оплати - LiqPay, повертаємо кошти через API
            if ($payment->payment_method === 'LiqPay') {
                $liqpayService = app(LiqPayService::class);
                
                // Повертаємо кошти через LiqPay
                $result = $liqpayService->refundPayment($payment->transaction_id, $amount, $comment);
                
                // Оновлюємо дані LiqPay
                $payment->update([
                    'liqpay_data' => array_merge($payment->liqpay_data ?? [], [
                        'refund_data' => $result,
                    ]),
                ]);
            }
            
            // Оновлюємо статус платежу
            $payment->update([
                'status' => PaymentStatus::REFUNDED,
            ]);
            
            // Деактивуємо підписку, якщо вона є
            $this->deactivateSubscription($payment);
            
            return $payment->load(['user', 'tariff']);
        });
    }
    
    /**
     * Деактивувати підписку, пов'язану з платежем.
     *
     * @param Payment $payment
     * @return void
     */
    private function deactivateSubscription(Payment $payment): void
    {
        try {
            // Знаходимо підписку, пов'язану з платежем
            $subscription = UserSubscription::where('payment_id', $payment->id)
                ->where('is_active', true)
                ->first();
            
            if ($subscription) {
                // Деактивуємо підписку
                $subscription->update([
                    'is_active' => false,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deactivating subscription: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
            ]);
        }
    }
}
