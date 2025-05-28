<?php

namespace AnimeSite\Actions\Studios;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Studio;

class UpdateStudio
{
    /**
     * @param Studio $studio
     * @param array{
     *     slug?: string,
     *     name?: string,
     *     description?: string,
     *     image?: string|UploadedFile|null,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: string|UploadedFile|null
     * } $data
     */
    public function __invoke(Studio $studio, array $data): Studio
    {
        Gate::authorize('update', $studio);

        return DB::transaction(function () use ($studio, $data) {
            // Handle image upload
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image'] = $studio->handleFileUpload($data['image'], 'studio', $studio->image);
            }

            // Handle meta_image upload
            if (isset($data['meta_image']) && $data['meta_image'] instanceof UploadedFile) {
                $data['meta_image'] = $studio->handleFileUpload($data['meta_image'], 'meta', $studio->meta_image);
            }

            // Update the studio
            $studio->update($data);

            return $studio->loadMissing(['animes']);
        });
    }
}
