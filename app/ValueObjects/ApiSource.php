<?php

namespace AnimeSite\ValueObjects;

use AnimeSite\Enums\ApiSourceName;
use JsonSerializable;

class ApiSource implements JsonSerializable
{
    public function __construct(
        public ApiSourceName $name,
        public string $id,
    ) {}

    /**
     * Convert to array for JSON serialization
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name->value,
            'id' => $this->id,
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
