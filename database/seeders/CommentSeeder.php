<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use AnimeSite\Models\Comment;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Selection;
use AnimeSite\Models\User;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        // Отримуємо всі наявні фільми, епізоди, добірки та користувачів
        $movies = Anime::all();
        $episodes = Episode::all();
        $selections = Selection::all();
        $users = User::all();
        $comments = Comment::all();

        // Додаємо коментарі до фільмів
        foreach ($movies as $movie) {
            Comment::factory()
                ->forCommentable($movie)
                ->forUser($users->random())
                ->count(rand(1, 5)) // Випадкова кількість коментарів
                ->create();
        }

        // Додаємо коментарі до епізодів
        foreach ($episodes as $episode) {
            Comment::factory()
                ->forCommentable($episode)
                ->forUser($users->random())
                ->count(rand(1, 5)) // Випадкова кількість коментарів
                ->create();
        }

        // Додаємо коментарі до добірок
        foreach ($selections as $selection) {
            Comment::factory()
                ->forCommentable($selection)
                ->forUser($users->random())
                ->count(rand(1, 5)) // Випадкова кількість коментарів
                ->create();
        }


        foreach ($comments as $comment) {
            Comment::factory()
                ->forCommentable($comment)
                ->forUser($users->random())
                ->count(rand(1, 5)) // Випадкова кількість коментарів
                ->create();
        }
    }
}
