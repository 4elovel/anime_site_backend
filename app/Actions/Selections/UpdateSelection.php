<?php

namespace AnimeSite\Actions\Selections;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use AnimeSite\Models\Selection;

class UpdateSelection
{
    /**
     * Оновлює існуючу добірку.
     *
     * @param Selection $selection
     * @param array{
     *     slug?: string,
     *     name?: string,
     *     description?: string|null,
     *     is_published?: bool,
     *     is_active?: bool,
     *     poster?: UploadedFile|null,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: UploadedFile|null
     * } $data
     * @return Selection
     */
    public function __invoke(Selection $selection, array $data): Selection
    {
        Gate::authorize('update', $selection);

        return DB::transaction(function () use ($selection, $data) {
            // Генеруємо slug на основі нової назви, якщо вона змінилась і slug не вказаний
            if (isset($data['name']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Обробляємо завантаження файлів
            if (isset($data['poster']) && $data['poster'] instanceof UploadedFile) {
                $data['poster'] = $selection->handleFileUpload(
                    $data['poster'],
                    'poster',
                    $selection->poster
                );
            }

            if (isset($data['meta_image']) && $data['meta_image'] instanceof UploadedFile) {
                $data['meta_image'] = $selection->handleFileUpload(
                    $data['meta_image'],
                    'meta',
                    $selection->meta_image
                );
            }

            // Оновлюємо добірку
            $selection->update($data);

            return $selection->loadMissing(['user', 'animes', 'persons', 'episodes']);
        });
    }
}
