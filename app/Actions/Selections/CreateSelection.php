<?php

namespace AnimeSite\Actions\Selections;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use AnimeSite\Models\Selection;

class CreateSelection
{
    /**
     * Створює новий запис добірки.
     *
     * @param array{
     *     name: string,
     *     slug?: string,
     *     description: string|null,
     *     user_id: string,
     *     is_published?: bool,
     *     is_active?: bool,
     *     poster?: UploadedFile|null,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: UploadedFile|null
     * } $data
     * @return Selection
     */
    public function __invoke(array $data): Selection
    {
        Gate::authorize('create', Selection::class);

        return DB::transaction(function () use ($data) {
            // Генеруємо slug, якщо він не вказаний
            if (!isset($data['slug']) || empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // За замовчуванням добірка опублікована та активна
            if (!isset($data['is_published'])) {
                $data['is_published'] = true;
            }

            if (!isset($data['is_active'])) {
                $data['is_active'] = true;
            }

            // Створюємо добірку
            $selection = Selection::create($data);

            // Обробляємо завантаження файлів
            if (isset($data['poster']) && $data['poster'] instanceof UploadedFile) {
                $selection->poster = $selection->handleFileUpload($data['poster'], 'poster');
                $selection->save();
            }

            if (isset($data['meta_image']) && $data['meta_image'] instanceof UploadedFile) {
                $selection->meta_image = $selection->handleFileUpload($data['meta_image'], 'meta');
                $selection->save();
            }

            return $selection->loadMissing(['user', 'animes', 'persons', 'episodes']);
        });
    }
}
