<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AnimeSite\Enums\PaymentStatus;
use AnimeSite\Models\Payment;
use AnimeSite\Models\Tariff;
use AnimeSite\Models\User;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $tariff = Tariff::inRandomOrder()->first() ?? Tariff::factory()->create();
        $status = $this->faker->randomElement([
            PaymentStatus::PENDING->value,
            PaymentStatus::SUCCESS->value,
            PaymentStatus::FAILED->value,
            PaymentStatus::REFUNDED->value,
        ]);

        return [
            'user_id' => User::factory(),
            'tariff_id' => $tariff->id,
            'amount' => $tariff->price,
            'currency' => $tariff->currency,
            'payment_method' => 'LiqPay',
            'transaction_id' => $this->faker->unique()->uuid(),
            'status' => $status,
            'liqpay_data' => $this->getLiqPayData($status),
        ];
    }

    /**
     * Generate mock LiqPay data based on payment status
     */
    private function getLiqPayData(string $status): array
    {
        $paymentId = $this->faker->uuid();

        return [
            'payment_id' => $paymentId,
            'status' => match ($status) {
                PaymentStatus::PENDING->value => 'wait_accept',
                PaymentStatus::SUCCESS->value => 'success',
                PaymentStatus::FAILED->value => 'error',
                PaymentStatus::REFUNDED->value => 'reversed',
                default => 'wait_accept',
            },
            'transaction_id' => $this->faker->uuid(),
            'order_id' => $this->faker->uuid(),
            'liqpay_order_id' => $this->faker->uuid(),
            'paytype' => $this->faker->randomElement(['card', 'privat24', 'masterpass']),
            'sender_card_mask2' => $this->faker->creditCardNumber(),
            'sender_card_bank' => $this->faker->company(),
            'sender_card_type' => $this->faker->randomElement(['visa', 'mastercard']),
            'sender_card_country' => 804,
            'ip' => $this->faker->ipv4(),
            'amount' => $this->faker->randomFloat(2, 49, 299),
            'currency' => 'UAH',
            'description' => 'Оплата підписки на сервіс аніме',
            'create_date' => $this->faker->unixTime(),
            'end_date' => $this->faker->unixTime(),
        ];
    }

    /**
     * Indicate that the payment is successful
     */
    public function successful(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => PaymentStatus::SUCCESS->value,
                'liqpay_data' => $this->getLiqPayData(PaymentStatus::SUCCESS->value),
            ];
        });
    }

    /**
     * Indicate that the payment is pending
     */
    public function pending(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => PaymentStatus::PENDING->value,
                'liqpay_data' => $this->getLiqPayData(PaymentStatus::PENDING->value),
            ];
        });
    }

    /**
     * Indicate that the payment failed
     */
    public function failed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => PaymentStatus::FAILED->value,
                'liqpay_data' => $this->getLiqPayData(PaymentStatus::FAILED->value),
            ];
        });
    }

    /**
     * Indicate that the payment was refunded
     */
    public function refunded(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => PaymentStatus::REFUNDED->value,
                'liqpay_data' => $this->getLiqPayData(PaymentStatus::REFUNDED->value),
            ];
        });
    }

    /**
     * Create a payment for a specific user
     */
    public function forUser(User $user): self
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }
}
