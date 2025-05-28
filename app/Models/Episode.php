<?php

namespace AnimeSite\Models;

use AnimeSite\Builders\EpisodeBuilder;
use Database\Factories\EpisodeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use AnimeSite\Casts\VideoPlayersCast;
use AnimeSite\Models\Traits\HasFiles;
use AnimeSite\Models\Traits\HasSeo;
use AnimeSite\ValueObjects\VideoPlayer;

/**
 * @mixin IdeHelperEpisode
 */
class Episode extends Model
{
    /** @use HasFactory<EpisodeFactory> */
    use HasFactory, HasSeo, HasUlids, HasFiles;

    protected $casts = [
        'pictures' => 'array',
        'video_players' => 'array',
        'air_date' => 'date',
    ];



    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class);
    }

    public function userLists(): MorphMany
    {
        return $this->morphMany(UserList::class, 'listable');
    }

    public function selections(): MorphToMany
    {
        return $this->morphToMany(Selection::class, 'selectionable', 'selectionables');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    protected function pictureUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->pictures->isNotEmpty()
                ? asset("storage/{$this->pictures->first()}")
                : null
        );
    }

    private function formatDuration(int $duration): string
    {
        $hours = floor($duration / 60);
        $minutes = $duration % 60;

        $formatted = [];

        if ($hours > 0) {
            $formatted[] = "{$hours} год";
        }

        if ($minutes > 0) {
            $formatted[] = "{$minutes} хв";
        }

        return implode(' ', $formatted);
    }

    public function newEloquentBuilder($query): EpisodeBuilder
    {
        return new EpisodeBuilder($query);
    }
}
