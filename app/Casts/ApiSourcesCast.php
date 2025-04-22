<?php

namespace Liamtseva\Cinema\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Liamtseva\Cinema\Enums\ApiSourceName;
use Liamtseva\Cinema\ValueObjects\ApiSource;

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
                $as = new ApiSource(ApiSourceName::from($as['source']), $as['id']);
            }

            return [
                'name' => $as->name->value,
                'id' => $as->id,
            ];
        })->toArray());
    }
}
