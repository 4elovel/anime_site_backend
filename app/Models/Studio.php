<?php

namespace AnimeSite\Models;

use AnimeSite\Builders\StudioBuilder;
use Database\Factories\StudioFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AnimeSite\Models\Traits\HasFiles;
use AnimeSite\Models\Traits\HasSeo;

/**
 * @mixin IdeHelperStudio
 */
class Studio extends Model
{
    /** @use HasFactory<StudioFactory> */
    use HasFactory, HasSeo, HasUlids, HasFiles;

    protected $hidden = ['searchable'];

    public function animes(): HasMany
    {
        return $this->hasMany(Anime::class);
    }

    public function newEloquentBuilder($query): StudioBuilder
    {
        return new StudioBuilder($query);
    }
}
