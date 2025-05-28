<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use AnimeSite\Enums\Gender;
use AnimeSite\Enums\PersonType;
use AnimeSite\Models\Person;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name();
        $type = $this->faker->randomElement(PersonType::cases());
        $gender = $this->faker->randomElement(Gender::cases());
        $birthday = $this->faker->optional(0.8)->dateTimeBetween('-80 years', '-18 years');

        return [
            'slug' => Str::slug($name),
            'name' => $name,
            'original_name' => $this->faker->optional(0.7)->name(),
            'gender' => $gender->value,
            'image' => $this->faker->imageUrl(640, 480, 'people'),
            'description' => $this->faker->paragraph(3),
            'birthday' => $birthday,
            'birthplace' => $this->faker->optional(0.7)->city() . ', ' . $this->faker->country(),
            'meta_title' => $type->name() . ' ' . $name . ' | ' . config('app.name'),
            'meta_description' => $this->faker->sentence(15),
            'meta_image' => $this->faker->imageUrl(1200, 630, 'people-meta', true),
            'type' => $type->value,
        ];
    }

}
