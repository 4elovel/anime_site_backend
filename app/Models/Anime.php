<?php

namespace AnimeSite\Models;

use AnimeSite\Builders\AnimeBuilder;
use Database\Factories\AnimeFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use AnimeSite\Casts\AnimeRelateCast;
use AnimeSite\Casts\ApiSourcesCast;
use AnimeSite\Casts\AttachmentsCast;
use AnimeSite\Enums\Country;
use AnimeSite\Enums\Kind;
use AnimeSite\Enums\Period;
use AnimeSite\Enums\RestrictedRating;
use AnimeSite\Enums\Source;
use AnimeSite\Enums\Status;
use AnimeSite\Enums\VideoQuality;
use AnimeSite\Models\Scopes\PublishedScope;
use AnimeSite\Models\Traits\HasFiles;
use AnimeSite\Models\Traits\HasSeo;

/**
 * @mixin IdeHelperAnime
 */
#[ScopedBy([PublishedScope::class])]
class Anime extends Model
{
    /** @use HasFactory<AnimeFactory> */
    use HasFactory, HasSeo, HasUlids, HasFiles;

    protected $casts = [
        'aliases' => AsCollection::class,
        'countries' => AsCollection::class,
        'attachments' => AsCollection::class,
        'related' => AsCollection::class,
        'similars' => AsCollection::class,
        'imdb_score' => 'float',
        'first_air_date' => 'date',
        'last_air_date' => 'date',
        'api_sources' => AsCollection::class,
        'kind' => Kind::class,
        'status' => Status::class,
        'period' => Period::class,
        'restricted_rating' => RestrictedRating::class,
        'source' => Source::class,
    ];

    protected $hidden = ['searchable'];

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class)->chaperone();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'anime_person', 'anime_id', 'person_id')
            ->withPivot('character_name', 'voice_person_id');
    }

    public function peoplePivot()
    {
        return $this->hasMany(Person::class, 'anime_person');
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class)->chaperone();
    }


    public function userLists(): MorphMany
    {
        return $this->morphMany(UserList::class, 'listable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function selections(): MorphToMany
    {
        return $this->morphToMany(Selection::class, 'selectionable', 'selectionables');
    }

    public function newEloquentBuilder($query): AnimeBuilder
    {
        return new AnimeBuilder($query);
    }
}
