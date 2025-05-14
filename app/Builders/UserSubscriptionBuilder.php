<?php

namespace AnimeSite\Builders;

use Illuminate\Database\Eloquent\Builder;

class UserSubscriptionBuilder extends Builder
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
     * Filter by active subscriptions
     *
     * @return $this
     */
    public function active(): self
    {
        $this->where('is_active', true)
            ->where('end_date', '>=', now());

        return $this;
    }

    /**
     * Filter by inactive subscriptions
     *
     * @return $this
     */
    public function inactive(): self
    {
        $this->where(function ($query) {
            $query->where('is_active', false)
                ->orWhere('end_date', '<', now());
        });

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
     * Filter by subscriptions about to expire
     *
     * @param int $days
     * @return $this
     */
    public function aboutToExpire(int $days = 3): self
    {
        $date = now()->addDays($days);

        $this->where('is_active', true)
            ->where('end_date', '<=', $date)
            ->where('end_date', '>=', now());

        return $this;
    }

    /**
     * Filter by subscriptions with auto-renew enabled
     *
     * @return $this
     */
    public function withAutoRenew(): self
    {
        $this->where('auto_renew', true);

        return $this;
    }

    /**
     * Filter by subscriptions with auto-renew disabled
     *
     * @return $this
     */
    public function withoutAutoRenew(): self
    {
        $this->where('auto_renew', false);

        return $this;
    }

    /**
     * Filter by subscriptions that started after a specific date
     *
     * @param string $date
     * @return $this
     */
    public function startedAfter(string $date): self
    {
        $this->where('start_date', '>=', $date);

        return $this;
    }

    /**
     * Filter by subscriptions that end before a specific date
     *
     * @param string $date
     * @return $this
     */
    public function endingBefore(string $date): self
    {
        $this->where('end_date', '<=', $date);

        return $this;
    }

    /**
     * Order by start date
     *
     * @param string $direction
     * @return $this
     */
    public function orderByStartDate(string $direction = 'desc'): self
    {
        $this->orderBy('start_date', $direction);

        return $this;
    }

    /**
     * Order by end date
     *
     * @param string $direction
     * @return $this
     */
    public function orderByEndDate(string $direction = 'asc'): self
    {
        $this->orderBy('end_date', $direction);

        return $this;
    }

    /**
     * Filter by recently created subscriptions
     *
     * @param int $days
     * @return $this
     */
    public function createdInLastDays(int $days = 30): self
    {
        $this->where('created_at', '>=', now()->subDays($days))
            ->orderByDesc('created_at');

        return $this;
    }
}
