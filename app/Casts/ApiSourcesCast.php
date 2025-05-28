<?php

namespace AnimeSite\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use AnimeSite\Enums\ApiSourceName;
use AnimeSite\ValueObjects\ApiSource;

class ApiSourcesCast implements CastsAttributes
{
    /**
     * @return Collection<ApiSource>
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): Collection
    {
        $collection = collect(json_decode($value, true));

        return $collection->isNotEmpty() ? $collection
            ->map(fn ($item) => new ApiSource(ApiSourceName::from($item['name']), $item['id'])) : $collection;
    }

    /**
     * @param  Collection<ApiSource>|array  $value
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (! $value instanceof Collection) {
            $value = collect($value);
        }

        return json_encode($value->map(function ($as) {
            if (is_array($as)) {
                // Конвертуємо масив у ApiSource
                $as = new ApiSource(
                    name: ApiSourceName::from($as['name'] ?? $as['source']),
                    id: $as['id']
                );
            }

            // Using JsonSerializable interface
            return $as;
        })->toArray());
    }
}
