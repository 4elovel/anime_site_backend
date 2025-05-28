<?php

namespace AnimeSite\ValueObjects;

use AnimeSite\Enums\AnimeRelateType;
use JsonSerializable;

class AnimeRelate implements JsonSerializable
{
    public function __construct(
        public string $anime_id,
        public AnimeRelateType $type,
    ) {}

    /**
     * Convert to array for JSON serialization
     */
    public function toArray(): array
    {
        return [
            'anime_id' => $this->anime_id,
            'type' => $this->type->value,
        ];
    }

    /**
     * Custom JSON serialization
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
