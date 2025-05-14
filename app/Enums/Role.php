<?php

namespace AnimeSite\Enums;

use phpDocumentor\Reflection\Types\Self_;

enum Role: string
{
    case USER = 'user';
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';

    public static function labels(): array
    {
        return [
            self::USER->value => 'Користувач',
            self::ADMIN->value => 'Адміністратор',
            self::MODERATOR->value => 'Модератор',
        ];
    }

    public function name(): string
    {
        return match ($this) {
            self::USER => 'Користувач',
            self::ADMIN => 'Адміністратор',
            self::MODERATOR => 'Модератор',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ADMIN => 'heroicon-s-bug-ant',
            self::MODERATOR => 'heroicon-s-star',
            self::USER => 'heroicon-s-user',
        };
    }

    public function getBadgeColor(): string
    {
        return match ($this) {
            self::ADMIN => 'danger',
            self::MODERATOR => 'warning',
            self::USER => 'success',
        };
    }
}
