<?php

namespace AnimeSite\Enums;

enum VideoPlayerName: string
{
    case KODIK = 'kodik';
    case ALOHA = 'aloha';

    public function name(): string
    {
        return match ($this) {
            self::KODIK => 'Kodik',
            self::ALOHA => 'Aloha',
        };
    }

    public static function labels(): array
    {
        return  [
            self::KODIK->value => 'kodik',
            self::ALOHA->value => 'aloha',
        ];
    }
}
