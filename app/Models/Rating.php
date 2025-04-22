<?php

namespace Liamtseva\Cinema\Models;

use Database\Factories\RatingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperRating
 */
class Rating extends Model
{
    /** @use HasFactory<RatingFactory> */
    use HasFactory, HasUlids;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Відношення з фільмом
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class);
    }

    public function review(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => nl2br(e($attributes['review'])),
            set: fn (mixed $value) => trim($value)
        );
    }

    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAnime(Builder $query, string $animeId): Builder
    {
        return $query->where('anime_id', $animeId);
    }

    public function scopeBetweenRatings(Builder $query, int $minRating, int $maxRating): Builder
    {
        return $query->whereBetween('number', [$minRating, $maxRating]);
    }
}
