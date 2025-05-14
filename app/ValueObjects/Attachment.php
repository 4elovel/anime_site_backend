<?php

namespace AnimeSite\ValueObjects;

use AnimeSite\Enums\AttachmentType;
use JsonSerializable;

class Attachment implements JsonSerializable
{
    public function __construct(
        public AttachmentType $type,
        public string $src,
        public string $title = '',
        public int $duration = 0
    ) {}

    /**
     * Convert to array for JSON serialization
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'src' => $this->src,
            'title' => $this->title,
            'duration' => $this->duration,
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
