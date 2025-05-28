<?php

namespace AnimeSite\Actions\People;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Person;

class CreatePerson
{
    /**
     * @param array{
     *     slug: string,
     *     name: string,
     *     original_name?: string|null,
     *     image?: string|UploadedFile|null,
     *     description?: string|null,
     *     birthday?: string|null,
     *     birthplace?: string|null,
     *     type: string,
     *     gender?: string|null,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: string|UploadedFile|null
     * } $data
     */
    public function __invoke(array $data): Person
    {
        Gate::authorize('create', Person::class);

        return DB::transaction(function () use ($data) {
            // Create the person first
            $person = Person::create($data);

            // Handle image upload
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $person->image = $person->handleFileUpload($data['image'], 'people');
            }

            // Handle meta_image upload
            if (isset($data['meta_image']) && $data['meta_image'] instanceof UploadedFile) {
                $person->meta_image = $person->handleFileUpload($data['meta_image'], 'meta');
            }

            // Save the changes
            $person->save();

            return $person->loadMissing(['animes']);
        });
    }
}
