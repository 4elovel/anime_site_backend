<?php

namespace AnimeSite\Models;

use AnimeSite\Builders\PersonBuilder;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use AnimeSite\Enums\Gender;
use AnimeSite\Enums\PersonType;
use AnimeSite\Models\Traits\HasFiles;
use AnimeSite\Models\Traits\HasSeo;

/**
 * @mixin IdeHelperPerson
 */
class Person extends Model
{
    /** @use HasFactory<PersonFactory> */
    use HasFactory, HasSeo, HasUlids, HasFiles;

    protected $casts = [
        'type' => PersonType::class,
        'gender' => Gender::class,
        'birthday' => 'date',
    ];

    public function animes(): BelongsToMany
    {
        return $this->belongsToMany(Anime::class, 'anime_person', 'person_id', 'anime_id')
            ->withPivot('character_name', 'voice_person_id');
    }

    public function userLists(): MorphMany
    {
        return $this->morphMany(UserList::class, 'listable');
    }

    public function selections(): MorphToMany
    {
        return $this->morphToMany(Selection::class, 'selectionable', 'selectionables');
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->original_name
                ? "{$this->name} ({$this->original_name})"
                : $this->name,
        );
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->birthday
                ? now()->diffInYears($this->birthday)
                : null,
        );
    }

    public function newEloquentBuilder($query): PersonBuilder
    {
        return new PersonBuilder($query);
    }
}
