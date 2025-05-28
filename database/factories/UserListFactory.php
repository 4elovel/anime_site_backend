<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AnimeSite\Enums\UserListType;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Person;
use AnimeSite\Models\Selection;
use AnimeSite\Models\Tag;
use AnimeSite\Models\User;
use AnimeSite\Models\UserList;

/**
 * @extends Factory<UserList>
 */
class UserListFactory extends Factory
{
    public function definition(): array
    {
        // Список доступних класів для `listable_type`
        $listableClasses = [
            Anime::class,
            Episode::class,
            Person::class,
            Tag::class,
            Selection::class,
        ];

        // Випадковий вибір класу
        $listableClass = $this->faker->randomElement($listableClasses);

        // Створення або вибір випадкового запису відповідного класу
        $listable = $listableClass::inRandomOrder()->first()
            ?? $listableClass::factory()->create();

        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'listable_id' => $listable->id,
            'listable_type' => $listableClass,
            'type' => $this->faker->randomElement(UserListType::cases())->value,
        ];
    }
}
