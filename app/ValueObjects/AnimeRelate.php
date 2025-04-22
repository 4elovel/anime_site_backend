<?php

namespace Liamtseva\Cinema\ValueObjects;

use Liamtseva\Cinema\Enums\AnimeRelateType;

class AnimeRelate
{
    public function __construct(
        public string $anime_id,
        public AnimeRelateType $type,
    ) {}
}
