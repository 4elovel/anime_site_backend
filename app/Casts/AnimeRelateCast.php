<?php

namespace Liamtseva\Cinema\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Liamtseva\Cinema\Enums\AnimeRelateType;
use Liamtseva\Cinema\ValueObjects\AnimeRelate;

class AnimeRelateCast implements CastsAttributes
{
    /**
     * @return Collection<AnimeRelate>
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $collection = collect(json_decode($value, true));

        return $collection->isNotEmpty() ? $collection
            ->map(fn ($item) => new AnimeRelate($item['anime_id'], AnimeRelateType::from($item['type']))) : $collection;
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

                return [
                    'anime_id' => $mr->anime_id,
                    'type' => $mr->type,
                ];
            })->filter()->toArray()  // .filter() видаляє null значення
        );
    }

}
