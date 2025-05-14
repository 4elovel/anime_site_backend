<?php

namespace AnimeSite\Actions\Tags;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use AnimeSite\Models\Tag;

class UpdateTag
{
    /**
     * @param Tag $tag
     * @param array{
     *     slug?: string,
     *     name?: string,
     *     description?: string,
     *     image?: string|null,
     *     aliases?: array,
     *     is_genre?: bool,
     *     parent_id?: string|null,
     *     meta_title?: string|null,
     *     meta_description?: string|null,
     *     meta_image?: string|null
     * } $data
     */
    public function __invoke(Tag $tag, array $data): Tag
    {
        Gate::authorize('update', $tag);

        return DB::transaction(function () use ($tag, $data) {
            $tag->update($data);
            return $tag->loadMissing(['parent', 'children']);
        });
    }
}
