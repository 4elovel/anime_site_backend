<?php

namespace AnimeSite\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use AnimeSite\Enums\AttachmentType;
use AnimeSite\ValueObjects\Attachment;

class AttachmentsCast implements CastsAttributes
{
    /**
     * @return Collection<Attachment>
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): Collection
    {
        $collection = collect(json_decode($value, true));

        return $collection->isNotEmpty() ? $collection
            ->map(fn ($item) => new Attachment(
                type: AttachmentType::from($item['type']),
                src: $item['src'],
                title: $item['title'] ?? '',
                duration: $item['duration'] ?? 0
            )) : $collection;
    }

    /**
     * @param  Collection<Attachment>|array  $value
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (! $value instanceof Collection) {
            $value = collect($value);
        }

        return json_encode($value->map(function ($item) {
            // Якщо елемент ще не є об'єктом Attachment, створюємо його
            if (is_array($item)) {
                $item = new Attachment(
                    type: AttachmentType::from($item['type']),
                    src: $item['src'],
                    title: $item['title'] ?? '',
                    duration: $item['duration'] ?? 0
                );
            }

            // Using JsonSerializable interface
            return $item;
        })->toArray());
    }
}
