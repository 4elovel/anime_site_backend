<?php

namespace AnimeSite\Models;

use AnimeSite\Models\Traits\HasSeo;
use Database\Factories\AchievementFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AnimeSite\Builders\AchievementBuilder;

/**
 * @mixin IdeHelperAchievement
 */
class Achievement extends Model
{
    /** @use HasFactory<AchievementFactory> */
    use HasFactory, HasUlids, HasSeo;
    public $timestamps = false;

    public function users(): BelongsToMany
    {
        return $this->BelongsToMany(User::class)
            ->withPivot('progress_count');
    }

    public function newEloquentBuilder($query): AchievementBuilder
    {
        return new AchievementBuilder($query);
    }
}
