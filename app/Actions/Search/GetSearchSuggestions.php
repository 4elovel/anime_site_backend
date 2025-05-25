<?php

namespace AnimeSite\Actions\Search;

use Illuminate\Support\Facades\DB;
use AnimeSite\Models\Anime;
use AnimeSite\Models\Person;
use AnimeSite\Models\Studio;
use AnimeSite\Models\Tag;
use AnimeSite\Models\Selection;

class GetSearchSuggestions
{
    /**
     * Отримати пошукові підказки на основі часткового запиту.
     *
     * @param string $query
     * @return array
     */
    public function __invoke(string $query): array
    {
        // Якщо запит порожній або занадто короткий, повертаємо порожній масив
        if (empty($query) || mb_strlen($query) < 2) {
            return [];
        }

        // Обмежуємо кількість підказок для кожного типу
        $limit = 3;

        // Отримуємо підказки для аніме
        $animeSuggestions = $this->getAnimeSuggestions($query, $limit);

        // Отримуємо підказки для людей
        $peopleSuggestions = $this->getPeopleSuggestions($query, $limit);

        // Отримуємо підказки для студій
        $studioSuggestions = $this->getStudioSuggestions($query, $limit);

        // Отримуємо підказки для тегів
        $tagSuggestions = $this->getTagSuggestions($query, $limit);

        // Отримуємо підказки для добірок
        $selectionSuggestions = $this->getSelectionSuggestions($query, $limit);

        // Об'єднуємо всі підказки
        return [
            'anime' => $animeSuggestions,
            'people' => $peopleSuggestions,
            'studios' => $studioSuggestions,
            'tags' => $tagSuggestions,
            'selections' => $selectionSuggestions,
        ];
    }

    /**
     * Отримати підказки для аніме.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    private function getAnimeSuggestions(string $query, int $limit): array
    {
        return Anime::query()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('original_title', 'like', "%{$query}%");
            })
            ->orderByRaw("
                CASE 
                    WHEN title LIKE ? THEN 1
                    WHEN title LIKE ? THEN 2
                    WHEN original_title LIKE ? THEN 3
                    WHEN original_title LIKE ? THEN 4
                    ELSE 5
                END
            ", [
                $query, // Точний збіг з назвою
                "{$query}%", // Починається з запиту
                $query, // Точний збіг з оригінальною назвою
                "{$query}%", // Починається з запиту (оригінальна назва)
            ])
            ->limit($limit)
            ->get(['id', 'title', 'original_title', 'poster', 'slug'])
            ->map(function ($anime) {
                return [
                    'id' => $anime->id,
                    'title' => $anime->title,
                    'original_title' => $anime->original_title,
                    'poster' => $anime->poster ? $anime->getFileUrl($anime->poster) : null,
                    'slug' => $anime->slug,
                    'type' => 'anime',
                    'url' => "/animes/{$anime->slug}",
                ];
            })
            ->toArray();
    }

    /**
     * Отримати підказки для людей.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    private function getPeopleSuggestions(string $query, int $limit): array
    {
        return Person::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('original_name', 'like', "%{$query}%");
            })
            ->orderByRaw("
                CASE 
                    WHEN name LIKE ? THEN 1
                    WHEN name LIKE ? THEN 2
                    WHEN original_name LIKE ? THEN 3
                    WHEN original_name LIKE ? THEN 4
                    ELSE 5
                END
            ", [
                $query, // Точний збіг з ім'ям
                "{$query}%", // Починається з запиту
                $query, // Точний збіг з оригінальним ім'ям
                "{$query}%", // Починається з запиту (оригінальне ім'я)
            ])
            ->limit($limit)
            ->get(['id', 'name', 'original_name', 'photo', 'slug'])
            ->map(function ($person) {
                return [
                    'id' => $person->id,
                    'title' => $person->name,
                    'original_title' => $person->original_name,
                    'photo' => $person->photo ? $person->getFileUrl($person->photo) : null,
                    'slug' => $person->slug,
                    'type' => 'person',
                    'url' => "/people/{$person->slug}",
                ];
            })
            ->toArray();
    }

    /**
     * Отримати підказки для студій.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    private function getStudioSuggestions(string $query, int $limit): array
    {
        return Studio::query()
            ->where('name', 'like', "%{$query}%")
            ->orderByRaw("
                CASE 
                    WHEN name LIKE ? THEN 1
                    WHEN name LIKE ? THEN 2
                    ELSE 3
                END
            ", [
                $query, // Точний збіг з назвою
                "{$query}%", // Починається з запиту
            ])
            ->limit($limit)
            ->get(['id', 'name', 'logo', 'slug'])
            ->map(function ($studio) {
                return [
                    'id' => $studio->id,
                    'title' => $studio->name,
                    'logo' => $studio->logo ? $studio->getFileUrl($studio->logo) : null,
                    'slug' => $studio->slug,
                    'type' => 'studio',
                    'url' => "/studios/{$studio->slug}",
                ];
            })
            ->toArray();
    }

    /**
     * Отримати підказки для тегів.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    private function getTagSuggestions(string $query, int $limit): array
    {
        return Tag::query()
            ->where('name', 'like', "%{$query}%")
            ->orderByRaw("
                CASE 
                    WHEN name LIKE ? THEN 1
                    WHEN name LIKE ? THEN 2
                    ELSE 3
                END
            ", [
                $query, // Точний збіг з назвою
                "{$query}%", // Починається з запиту
            ])
            ->limit($limit)
            ->get(['id', 'name', 'is_genre', 'slug'])
            ->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'title' => $tag->name,
                    'is_genre' => $tag->is_genre,
                    'slug' => $tag->slug,
                    'type' => 'tag',
                    'url' => "/tags/{$tag->slug}",
                ];
            })
            ->toArray();
    }

    /**
     * Отримати підказки для добірок.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    private function getSelectionSuggestions(string $query, int $limit): array
    {
        return Selection::query()
            ->where('title', 'like', "%{$query}%")
            ->where('is_published', true)
            ->where('is_active', true)
            ->orderByRaw("
                CASE 
                    WHEN title LIKE ? THEN 1
                    WHEN title LIKE ? THEN 2
                    ELSE 3
                END
            ", [
                $query, // Точний збіг з назвою
                "{$query}%", // Починається з запиту
            ])
            ->limit($limit)
            ->get(['id', 'title', 'poster', 'slug'])
            ->map(function ($selection) {
                return [
                    'id' => $selection->id,
                    'title' => $selection->title,
                    'poster' => $selection->poster ? $selection->getFileUrl($selection->poster) : null,
                    'slug' => $selection->slug,
                    'type' => 'selection',
                    'url' => "/selections/{$selection->slug}",
                ];
            })
            ->toArray();
    }
}
