<?php

namespace AnimeSite\Actions\Animes;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Anime;

class CreateAnime
{
    /**
     * @param array{
     *     slug: string,
     *     name: string,
     *     description: string,
     *     image_name: string|UploadedFile,
     *     studio_id: string,
     *     countries: array,
     *     poster: string|UploadedFile|null,
     *     imdb_score: float|null,
     *     first_air_date: string|null,
     *     last_air_date: string|null,
     *     attachments: array,
     *     related: array|null,
     *     similars: array,
     *     is_published: bool,
     *     meta_title: string|null,
     *     meta_description: string|null,
     *     meta_image: string|UploadedFile|null,
     * } $data
     */
    public function __invoke(array $data): Anime
    {
        Gate::authorize('create', Anime::class);

        return DB::transaction(function () use ($data) {
            // Create the anime record first
            $anime = Anime::create($data);

            // Handle file uploads
            if (isset($data['image_name']) && $data['image_name'] instanceof UploadedFile) {
                $anime->image_name = $anime->handleFileUpload($data['image_name'], 'images');
            }

            if (isset($data['poster']) && $data['poster'] instanceof UploadedFile) {
                $anime->poster = $anime->handleFileUpload($data['poster'], 'posters');
            }

            if (isset($data['meta_image']) && $data['meta_image'] instanceof UploadedFile) {
                $anime->meta_image = $anime->handleFileUpload($data['meta_image'], 'meta');
            }

            // Process attachments if they exist
            if (isset($data['attachments']) && is_array($data['attachments'])) {
                $anime->attachments = $anime->processAttachments($data['attachments'], 'attachments');
            }

            // Save the changes
            $anime->save();

            return $anime;
        });
    }
}
