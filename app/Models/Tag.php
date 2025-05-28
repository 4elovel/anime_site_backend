<?php

namespace AnimeSite\Models;

use AnimeSite\Builders\TagBuilder;
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
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use AnimeSite\Models\Traits\HasFiles;
use AnimeSite\Models\Traits\HasSeo;

/**
 * @mixin IdeHelperTag
 */
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory, HasSeo, HasUlids, HasFiles;
    protected $fillable = [
        'slug',
        'name',
        'description',
        'image',
        'aliases',
        'is_genre',
        'meta_title',
        'meta_description',
        'meta_image',
        'parent_id',
    ];

    protected $hidden = ['taggables'];

    protected $casts = [
        'aliases' => 'array',
        'is_genre' => 'boolean',
    ];


    /**
     * Зв'язок з аніме (поліморфний)
     */
    public function animes(): MorphToMany
    {
        return $this->morphedByMany(Anime::class, 'taggable', 'taggables');
    }

    /**
     * Зв'язок з персонами (поліморфний)
     */
    public function people(): MorphToMany
    {
        return $this->morphedByMany(Person::class, 'taggable', 'taggables');
    }

    /**
     * Зв'язок з добірками (поліморфний)
     */
    public function selections(): MorphToMany
    {
        return $this->morphedByMany(Selection::class, 'taggable', 'taggables');
    }

    /**
     * Зв'язок з усіма моделями, які мають теги
     */
    public function taggables(): MorphToMany
    {
        return $this->morphedByMany(Model::class, 'taggable', 'taggables');
    }

    public function userLists(): MorphMany
    {
        return $this->morphMany(UserList::class, 'listable');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function newEloquentBuilder($query): TagBuilder
    {
        return new TagBuilder($query);
    }
}
