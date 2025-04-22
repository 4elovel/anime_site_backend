<?php

namespace Liamtseva\Cinema\Models;

use Database\Factories\NotificationHistoryFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Liamtseva\Cinema\Enums\NotificationType;
use Liamtseva\Cinema\Enums\UserListType;

/**
 * @mixin IdeHelperNotificationHistory
 */
class NotificationHistory extends Model
{
    /** @use HasFactory<NotificationHistoryFactory> */
    use HasFactory, HasUlids;

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeOfType(Builder $query, NotificationType $type): Builder
    {
        return $query->where('notifiable_type', $type->value);
    }

    public function scopeForUser(Builder $query,
        string $userId,
        ?string $notifiableClass = null,
        ?NotificationType $notificationTypeType = null): Builder
    {
        return $query->where('user_id', $userId)
            ->when($notifiableClass, function ($query) use ($notifiableClass) {
                $query->where('notifiable_type', $notifiableClass);
            });
    }

    public function scopeOnlyViewed(Builder $query): Builder
    {
        return $query->where('is_viewed', true);
    }

    public function scopeOnlyNotViewed(Builder $query): Builder
    {
        return $query->where('is_viewed', false);
    }

}
