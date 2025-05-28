<?php

namespace AnimeSite\Actions\Payments;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Payment;
use AnimeSite\Services\LiqPayService;

class CheckPaymentStatus
{
    /**
     * Перевірити статус платежу за ID транзакції.
     *
     * @param string $transactionId
     * @return Payment
     */
    public function __invoke(string $transactionId): Payment
    {
        // Знаходимо платіж за ID транзакції
        $payment = Payment::where('transaction_id', $transactionId)->firstOrFail();
        
        Gate::authorize('view', $payment);
        
        // Якщо платіж вже завершений, просто повертаємо його
        if (!$payment->isPending()) {
            return $payment->load(['user', 'tariff']);
        }
        
        // Якщо платіж в очікуванні і метод оплати - LiqPay, перевіряємо статус
        if ($payment->payment_method === 'LiqPay') {
            $liqpayService = app(LiqPayService::class);
            
            // Отримуємо статус платежу від LiqPay
            $status = $liqpayService->checkStatus($payment->transaction_id);
            
            // Якщо статус змінився, оновлюємо платіж
            if ($status && $status !== $payment->status->value) {
                // Використовуємо екшин для обробки колбеку
                $processCallback = app(ProcessPaymentCallback::class);
                
                // Створюємо дані для обробки
                $callbackData = $liqpayService->createCallbackData($payment->transaction_id, $status);
                
                // Обробляємо дані як колбек
                return $processCallback($callbackData);
            }
        }
        
        return $payment->load(['user', 'tariff']);
    }
}
