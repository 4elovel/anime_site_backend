<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use AnimeSite\Models\Comment;
use AnimeSite\Models\Episode;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Selection;
use AnimeSite\Models\User;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    public function definition(): array
    {
        // Список доступних класів для `commentable_type`
        $commentableClasses = [
            Anime::class,
            Episode::class,
            Selection::class,
        ];

        // Випадковий вибір класу
        $commentableClass = $this->faker->randomElement($commentableClasses);

        // Створення або вибір випадкового запису відповідного класу
        $commentable = $commentableClass::query()->inRandomOrder()->first()
            ?? $commentableClass::factory()->create();

        return [
            'commentable_id' => $commentable->id,
            'commentable_type' => $commentableClass,
            'user_id' => User::inRandomOrder()->value('id'),
            'is_spoiler' => $this->faker->boolean(10), // 10% ймовірність, що це спойлер
            'body' => $this->faker->paragraph(),
        ];
    }


    /**
     * Встановлює коментар як відповідь на інший коментар.
     */
    public function replyTo(Comment $parentComment): self
    {
        return $this->state(fn () => [
            'commentable_id' => $parentComment->commentable_id,
            'commentable_type' => $parentComment->commentable_type,
        ]);
    }

    /**
     * Встановлює поліморфний зв'язок з вказаним типом і ID.
     */
    public function forCommentable(Model $commentable): self
    {
        return $this->state(fn () => [
            'commentable_id' => $commentable->id,
            'commentable_type' => get_class($commentable),
        ]);
    }

    /**
     * Встановлює користувача, який залишив коментар.
     */
    public function forUser(User $user): self
    {
        return $this->state(fn () => [
            'user_id' => $user->id,
        ]);
    }
}
