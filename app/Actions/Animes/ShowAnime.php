<?php

namespace AnimeSite\Actions\Animes;

use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Anime;

class ShowAnime
{
    /**
     * Отримати інформацію про конкретне аніме.
     *
     * @param Anime $anime
     * @return Anime
     */
    public function __invoke(Anime $anime): Anime
    {
        // Дозволяємо перегляд аніме без авторизації
        // Gate::authorize('view', $anime);

        // Завантажуємо зв'язані дані
        return $anime->loadMissing([
            'studio',
            'tags',
            'people',
            'episodes',
            'ratings',
            'comments',
            'comments.user',
            'selections',
        ])->loadAvg('ratings', 'number');
    }
}
