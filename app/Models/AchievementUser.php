<?php

namespace AnimeSite\Models;

use Database\Factories\AchievementUserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AnimeSite\Builders\AchievementUserBuilder;

/**
 * @mixin IdeHelperAchievementUser
 */
class AchievementUser extends Model
{
    /** @use HasFactory<AchievementUserFactory> */
    use HasFactory, HasUlids;

    public $timestamps = false;

    protected $table = 'achievement_user';

    protected $fillable = [
        'id',
        'user_id',
        'achievement_id',
        'progress_count',
    ];
    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function newEloquentBuilder($query): AchievementUserBuilder
    {
        return new AchievementUserBuilder($query);
    }
}
