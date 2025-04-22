<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Liamtseva\Cinema\Models\Anime;
use Liamtseva\Cinema\Models\Person;
use Liamtseva\Cinema\Models\Selection;

// TODO: відрефакторити
class SelectionSeeder extends Seeder
{
    public function run(): void
    {
        // Створюємо кілька selection
        $selections = Selection::factory(20)->create();

        // Додаємо фільми та персони до кожної підбірки
        $selections->each(function (Selection $selection) {
            // Вибираємо унікальні фільми та персони для підбірки
            $animes = Anime::inRandomOrder()->take(rand(5, 10))->pluck('id');
            $persons = Person::inRandomOrder()->take(rand(5, 10))->pluck('id');

            $selection->animes()->attach($animes);
            $selection->persons()->attach($persons);
        });
    }
}
