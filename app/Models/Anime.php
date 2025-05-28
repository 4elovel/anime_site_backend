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

    /**
     * Зв'язок з тегами (поліморфний)
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable', 'taggables');
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

    /**
     * Get similar anime based on the 'similars' field
     *
     * @return Builder
     */
    public function getSimilarAnime(): Builder
    {
        // Якщо поле similars порожнє, повертаємо порожній запит
        if (empty($this->similars)) {
            return self::query()->whereRaw('1 = 0'); // Завжди повертає порожній результат
        }

        // Отримуємо аніме за ID зі списку similars
        return self::query()->whereIn('id', $this->similars);
    }

    /**
     * Get related anime based on the 'related' field
     *
     * @return Builder
     */
    public function getRelatedAnime(): Builder
    {
        // Якщо поле related порожнє, повертаємо порожній запит
        if (empty($this->related)) {
            return self::query()->whereRaw('1 = 0'); // Завжди повертає порожній результат
        }

        // Отримуємо ID аніме зі списку related
        $relatedIds = collect($this->related)->pluck('anime_id')->toArray();

        // Отримуємо аніме за ID
        return self::query()->whereIn('id', $relatedIds);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
