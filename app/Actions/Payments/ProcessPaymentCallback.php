<?php

namespace AnimeSite\Actions\Payments;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use AnimeSite\Enums\PaymentStatus;
use AnimeSite\Models\Payment;
use AnimeSite\Models\UserSubscription;
use AnimeSite\Services\LiqPayService;

class ProcessPaymentCallback
{
    /**
     * Обробити колбек від платіжної системи.
     *
     * @param array{
     *     data: string,
     *     signature: string
     * } $data
     * @return Payment
     */
    public function __invoke(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $liqpayService = app(LiqPayService::class);
            
            // Перевіряємо підпис
            $decodedData = $liqpayService->decodeAndVerifyData($data['data'], $data['signature']);
            
            // Знаходимо платіж за ID транзакції
            $payment = Payment::where('transaction_id', $decodedData['order_id'])->firstOrFail();
            
            // Оновлюємо статус платежу
            $status = match ($decodedData['status'] ?? '') {
                'success', 'wait_accept' => PaymentStatus::SUCCESS,
                'failure', 'error' => PaymentStatus::FAILED,
                'reversed', 'refund' => PaymentStatus::REFUNDED,
                default => PaymentStatus::PENDING,
            };
            
            // Оновлюємо платіж
            $payment->update([
                'status' => $status,
                'liqpay_data' => array_merge($payment->liqpay_data ?? [], [
                    'callback_data' => $decodedData,
                ]),
            ]);
            
            // Якщо платіж успішний, створюємо підписку
            if ($payment->isSuccessful()) {
                $this->createOrUpdateSubscription($payment);
            }
            
            return $payment->load(['user', 'tariff']);
        });
    }
    
    /**
     * Створити або оновити підписку користувача.
     *
     * @param Payment $payment
     * @return void
     */
    private function createOrUpdateSubscription(Payment $payment): void
    {
        try {
            // Перевіряємо, чи є активна підписка
            $activeSubscription = UserSubscription::where('user_id', $payment->user_id)
                ->where('is_active', true)
                ->first();
            
            if ($activeSubscription) {
                // Якщо є активна підписка, продовжуємо її
                $newExpiresAt = $activeSubscription->expires_at->addDays($payment->tariff->duration_days);
                
                $activeSubscription->update([
                    'expires_at' => $newExpiresAt,
                    'tariff_id' => $payment->tariff_id,
                ]);
            } else {
                // Якщо немає активної підписки, створюємо нову
                UserSubscription::create([
                    'user_id' => $payment->user_id,
                    'tariff_id' => $payment->tariff_id,
                    'payment_id' => $payment->id,
                    'is_active' => true,
                    'expires_at' => now()->addDays($payment->tariff->duration_days),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error creating subscription: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'tariff_id' => $payment->tariff_id,
            ]);
            
            throw $e;
        }
    }
}
