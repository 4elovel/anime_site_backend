<?php

namespace AnimeSite\Actions\Episodes;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Episode;

class UpdateEpisode
{
    /**
     * @param Episode $episode
     * @param array{
     *     number?: int,
     *     slug?: string,
     *     name?: string,
     *     description?: string|null,
     *     duration?: int|null,
     *     air_date?: string|null,
     *     is_filler?: bool,
     *     pictures?: array|UploadedFile[],
     *     video_players?: array,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: string|UploadedFile|null
     * } $data
     */
    public function __invoke(Episode $episode, array $data): Episode
    {
        Gate::authorize('update', $episode);

        return DB::transaction(function () use ($episode, $data) {
            // Handle pictures array (could be UploadedFile objects)
            if (isset($data['pictures']) && is_array($data['pictures'])) {
                // Use the processFilesArray method from HasFiles trait
                $data['pictures'] = $episode->processFilesArray(
                    $data['pictures'],
                    'episodes',
                    $episode->pictures ?? []
                );
            }

            // Handle meta_image if it's an UploadedFile
            if (isset($data['meta_image']) && $data['meta_image'] instanceof UploadedFile) {
                $data['meta_image'] = $episode->handleFileUpload(
                    $data['meta_image'],
                    'meta',
                    $episode->meta_image
                );
            }

            // Update the episode
            $episode->update($data);

            return $episode;
        });
    }
}
