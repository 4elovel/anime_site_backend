<?php

namespace Liamtseva\Cinema\Models;

use Database\Factories\AchievementFactory;
use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperAchievement
 */
class Achievement extends Model
{
    /** @use HasFactory<AchievementFactory> */
    use HasFactory, HasUlids;
    public $timestamps = false;

    public function users(): BelongsToMany
    {
        return $this->BelongsToMany(User::class)
            ->withPivot('progress_count');
    }

    public function scopeByUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
