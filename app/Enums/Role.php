<?php

namespace Liamtseva\Cinema\Enums;

enum Role: string
{
    case USER = 'user';
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';

    public static function labels(): array
    {
        return [
            self::USER->value => 'user',
            self::ADMIN->value => 'admin',
            self::MODERATOR->value => 'moderator',
        ];
    }
}
