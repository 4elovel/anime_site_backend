<?php

namespace Liamtseva\Cinema\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Liamtseva\Cinema\Models\Traits\HasSeo;

/**
 * @mixin IdeHelperTag
 */
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory, HasSeo, HasUlids;

    public function scopeGenres($query)
    {
        return $query->where('is_genre', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'LIKE', "%{$term}%")
            ->orWhere('slug', 'LIKE', "%{$term}%");
    }

    public function animes(): BelongsToMany
    {
        return $this->belongsToMany(Anime::class);
    }

    public function userLists(): MorphMany
    {
        return $this->morphMany(UserList::class, 'listable');
    }

    protected function casts(): array
    {
        return [
            'aliases' => AsCollection::class,
        ];
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? url("storage/$value") : null
        );
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
