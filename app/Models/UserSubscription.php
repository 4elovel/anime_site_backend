<?php

namespace AnimeSite\Models;

use Database\Factories\UserSubscriptionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AnimeSite\Builders\UserSubscriptionBuilder;

/**
 * @mixin IdeHelperUserSubscription
 */
class UserSubscription extends Model
{
    /** @use HasFactory<UserSubscriptionFactory> */
    use HasFactory, HasUlids;

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'auto_renew' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    public function remainingDays(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->end_date);
    }

    public function newEloquentBuilder($query): UserSubscriptionBuilder
    {
        return new UserSubscriptionBuilder($query);
    }
}
