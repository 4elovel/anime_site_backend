<?php

namespace Liamtseva\Cinema\Models;

use Database\Factories\NotificationHistoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Liamtseva\Cinema\Enums\NotificationType;

/**
 * @mixin IdeHelperNotificationHistory
 */
class NotificationHistory extends Model
{
    /** @use HasFactory<NotificationHistoryFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'user_id',
        'notifiable_type',
        'notifiable_id',
        'type',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOfType(Builder $query, NotificationType $type): Builder
    {
        return $query->where('type', $type->value);
    }

    public function scopeForUser(Builder $query,
        string $userId,
        ?string $notifiableClass = null,
        ?NotificationType $notificationType = null): Builder
    {
        return $query->where('user_id', $userId)
            ->when($notifiableClass, function ($query) use ($notifiableClass) {
                $query->where('notifiable_type', $notifiableClass);
            })
            ->when($notificationType, function ($query) use ($notificationType) {
                $query->where('type', $notificationType->value);
            });
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead(): self
    {
        $this->update(['read_at' => now()]);
        return $this;
    }
}
