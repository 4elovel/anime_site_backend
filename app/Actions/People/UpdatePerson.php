<?php

namespace AnimeSite\Actions\People;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Person;

class UpdatePerson
{
    /**
     * @param Person $person
     * @param array{
     *     slug?: string,
     *     name?: string,
     *     original_name?: string|null,
     *     image?: string|UploadedFile|null,
     *     description?: string|null,
     *     birthday?: string|null,
     *     birthplace?: string|null,
     *     type?: string,
     *     gender?: string|null,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: string|UploadedFile|null
     * } $data
     */
    public function __invoke(Person $person, array $data): Person
    {
        Gate::authorize('update', $person);

        return DB::transaction(function () use ($person, $data) {
            // Handle image upload
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image'] = $person->handleFileUpload($data['image'], 'people', $person->image);
            }

            // Handle meta_image upload
            if (isset($data['meta_image']) && $data['meta_image'] instanceof UploadedFile) {
                $data['meta_image'] = $person->handleFileUpload($data['meta_image'], 'meta', $person->meta_image);
            }

            // Update the person
            $person->update($data);

            return $person->loadMissing(['animes']);
        });
    }
}
