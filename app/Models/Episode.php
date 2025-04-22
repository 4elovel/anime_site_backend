<?php

namespace Liamtseva\Cinema\Models;

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
use Illuminate\Support\Carbon;
use Liamtseva\Cinema\Casts\VideoPlayersCast;
use Liamtseva\Cinema\Models\Traits\HasSeo;
use Liamtseva\Cinema\ValueObjects\VideoPlayer;

/**
 * @mixin IdeHelperEpisode
 */
class Episode extends Model
{
    /** @use HasFactory<EpisodeFactory> */
    use HasFactory, HasSeo, HasUlids;

    public function scopeFor(Builder $query, string $animeId): Builder
    {
        return $query->where('anime_id', $animeId);
    }

    public function scopeAiredAfter(Builder $query, Carbon $date): Builder
    {
        return $query->where('air_date', '>=', $date);
    }

    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class);
    }

    public function videoPlayers(): HasMany
    {
        return $this->hasMany(VideoPlayer::class);
    }
    public function userLists(): MorphMany
    {
        return $this->morphMany(UserList::class, 'listable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    protected function casts(): array
    {
        return [
            'pictures' => AsCollection::class,
            'video_players' => VideoPlayersCast::class,
            'air_date' => 'date',
        ];
    }

    protected function pictureUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->pictures->isNotEmpty()
                ? asset("storage/{$this->pictures->first()}")
                : null
        );
    }

    protected function picturesUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->pictures->isNotEmpty()
                ? $this->pictures->map(fn ($picture) => asset("storage/{$picture}"))
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


}
