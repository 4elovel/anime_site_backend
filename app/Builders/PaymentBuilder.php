<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;
use AnimeSite\Enums\PaymentStatus;

class PaymentBuilder extends Builder
{
    /**
     * Order by created date
     *
     * @param string $direction
     * @return $this
     */
    public function orderByCreatedAt(string $direction = 'desc'): self
    {
        $this->orderBy('created_at', $direction);

        return $this;
    }

    /**
     * Filter by payment status
     *
     * @param PaymentStatus $status
     * @return $this
     */
    public function withStatus(PaymentStatus $status): self
    {
        $this->where('status', $status->value);

        return $this;
    }

    /**
     * Filter by successful payments
     *
     * @return $this
     */
    public function successful(): self
    {
        $this->where('status', PaymentStatus::SUCCESS->value);

        return $this;
    }

    /**
     * Filter by failed payments
     *
     * @return $this
     */
    public function failed(): self
    {
        $this->where('status', PaymentStatus::FAILURE->value);

        return $this;
    }

    /**
     * Filter by pending payments
     *
     * @return $this
     */
    public function pending(): self
    {
        $this->where('status', PaymentStatus::PENDING->value);

        return $this;
    }

    /**
     * Filter by user
     *
     * @param string $userId
     * @return $this
     */
    public function forUser(string $userId): self
    {
        $this->where('user_id', $userId);

        return $this;
    }

    /**
     * Filter by tariff
     *
     * @param string $tariffId
     * @return $this
     */
    public function forTariff(string $tariffId): self
    {
        $this->where('tariff_id', $tariffId);

        return $this;
    }

    /**
     * Filter by minimum amount
     *
     * @param float $amount
     * @return $this
     */
    public function withMinAmount(float $amount): self
    {
        $this->where('amount', '>=', $amount);

        return $this;
    }

    /**
     * Filter by maximum amount
     *
     * @param float $amount
     * @return $this
     */
    public function withMaxAmount(float $amount): self
    {
        $this->where('amount', '<=', $amount);

        return $this;
    }

    /**
     * Filter by amount range
     *
     * @param float $min
     * @param float $max
     * @return $this
     */
    public function withAmountRange(float $min, float $max): self
    {
        $this->whereBetween('amount', [$min, $max]);

        return $this;
    }

    /**
     * Filter by payments created after a specific date
     *
     * @param string $date
     * @return $this
     */
    public function after(string $date): self
    {
        $this->where('created_at', '>=', $date);

        return $this;
    }

    /**
     * Filter by payments created before a specific date
     *
     * @param string $date
     * @return $this
     */
    public function before(string $date): self
    {
        $this->where('created_at', '<=', $date);

        return $this;
    }
}
