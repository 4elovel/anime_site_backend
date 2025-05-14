<?php

namespace AnimeSite\Actions\Episodes;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Episode;

class CreateEpisode
{
    /**
     * @param array{
     *     anime_id: string,
     *     number: int,
     *     slug: string,
     *     name: string,
     *     description: string|null,
     *     duration: int|null,
     *     air_date: string|null,
     *     is_filler: bool,
     *     pictures: array|UploadedFile[],
     *     video_players: array,
     *     meta_title: string|null,
     *     meta_description: string|null,
     *     meta_image: string|UploadedFile|null
     * } $data
     */
    public function __invoke(array $data): Episode
    {
        Gate::authorize('create', Episode::class);

        return DB::transaction(function () use ($data) {
            // Create the episode first
            $episode = Episode::create($data);

            // Handle pictures array (could be UploadedFile objects)
            if (isset($data['pictures']) && is_array($data['pictures'])) {
                $processedPictures = [];
                foreach ($data['pictures'] as $picture) {
                    if ($picture instanceof UploadedFile) {
                        // Store each picture and add the path to the array
                        $path = $episode->handleFileUpload($picture, 'episodes');
                        if ($path) {
                            $processedPictures[] = $path;
                        }
                    } elseif (is_string($picture)) {
                        // Keep existing picture paths
                        $processedPictures[] = $picture;
                    }
                }
                $episode->pictures = $processedPictures;
            }

            // Handle meta_image if it's an UploadedFile
            if (isset($data['meta_image']) && $data['meta_image'] instanceof UploadedFile) {
                $episode->meta_image = $episode->handleFileUpload($data['meta_image'], 'meta');
            }

            // Save the changes
            $episode->save();

            return $episode;
        });
    }
}
