<?php

namespace AnimeSite\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use AnimeSite\Enums\AnimeRelateType;
use AnimeSite\ValueObjects\AnimeRelate;

class AnimeRelateCast implements CastsAttributes
{
    /**
     * @return Collection<AnimeRelate>
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $collection = collect(json_decode($value, true));

        return $collection->isNotEmpty() ? $collection
            ->map(fn ($item) => new AnimeRelate(
                anime_id: $item['anime_id'],
                type: AnimeRelateType::from($item['type'])
            )) : $collection;
    }

    /**
     * @param  Collection<AnimeRelate>|array  $value
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (! $value instanceof Collection) {
            $value = collect($value);
        }

        // Перевірка значень перед тим, як їх відправити у json
        return json_encode(
            $value->map(function (AnimeRelate $mr) {
                // Перевірка на порожні значення
                if (empty($mr->anime_id) || empty($mr->type)) {
                    // Якщо anime_id або type порожні, можна повернути порожній масив або зробити іншу обробку
                    return null;
                }

                // Using JsonSerializable interface
                return $mr;
            })->filter()->toArray()  // .filter() видаляє null значення
        );
    }

}
