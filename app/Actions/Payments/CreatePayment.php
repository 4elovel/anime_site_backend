<?php

namespace AnimeSite\Actions\Payments;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use AnimeSite\Enums\PaymentStatus;
use AnimeSite\Models\Payment;
use AnimeSite\Services\LiqPayService;

class CreatePayment
{
    /**
     * Створити новий платіж (платіжний намір).
     *
     * @param array{
     *     user_id: string,
     *     tariff_id: string,
     *     amount: float,
     *     currency: string,
     *     payment_method: string,
     *     return_url?: string|null
     * } $data
     * @return Payment
     */
    public function __invoke(array $data): Payment
    {
        Gate::authorize('create', Payment::class);

        return DB::transaction(function () use ($data) {
            // Генеруємо унікальний ID транзакції
            $transactionId = 'pay_' . Str::random(20);
            
            // Створюємо платіж
            $payment = Payment::create([
                'user_id' => $data['user_id'],
                'tariff_id' => $data['tariff_id'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'payment_method' => $data['payment_method'],
                'transaction_id' => $transactionId,
                'status' => PaymentStatus::PENDING,
            ]);
            
            // Якщо метод оплати - LiqPay, створюємо платіжну форму
            if ($data['payment_method'] === 'LiqPay') {
                $liqpayService = app(LiqPayService::class);
                
                $liqpayData = $liqpayService->createPayment([
                    'order_id' => $payment->transaction_id,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'description' => 'Оплата тарифу ' . $payment->tariff->name,
                    'result_url' => $data['return_url'] ?? config('app.url'),
                    'server_url' => route('api.v1.payments.callback'),
                ]);
                
                // Зберігаємо дані LiqPay для подальшого використання
                $payment->update([
                    'liqpay_data' => $liqpayData,
                ]);
            }
            
            return $payment->load(['user', 'tariff']);
        });
    }
}
