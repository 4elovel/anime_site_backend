<?php

namespace AnimeSite\Actions\Studios;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Studio;

class CreateStudio
{
    /**
     * @param array{
     *     slug: string,
     *     name: string,
     *     description: string,
     *     image?: string|UploadedFile|null,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: string|UploadedFile|null
     * } $data
     */
    public function __invoke(array $data): Studio
    {
        Gate::authorize('create', Studio::class);

        return DB::transaction(function () use ($data) {
            // Create the studio first
            $studio = Studio::create($data);

            // Handle image upload
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $studio->image = $studio->handleFileUpload($data['image'], 'studio');
            }

            // Handle meta_image upload
            if (isset($data['meta_image']) && $data['meta_image'] instanceof UploadedFile) {
                $studio->meta_image = $studio->handleFileUpload($data['meta_image'], 'meta');
            }

            // Save the changes
            $studio->save();

            return $studio->loadMissing(['animes']);
        });
    }
}
