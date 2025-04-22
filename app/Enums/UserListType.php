<?php

namespace Liamtseva\Cinema\Enums;

enum UserListType: string
{
    case FAVORITE = 'favorite';
    case NOT_WATCHING = 'not watching';
    case WATCHING = 'watching';
    case PLANNED = 'planned';
    case STOPPED = 'stopped';
    case REWATCHING = 'rewatching';
    case WATCHED = 'watched';

    public function name(): string
    {
        return match ($this) {
            self::FAVORITE => 'Улюблене',
            self::NOT_WATCHING => 'Не дивлюся',
            self::WATCHING => 'Дивлюся',
            self::PLANNED => 'В планах',
            self::STOPPED => 'Перестав',
            self::REWATCHING => 'Передивляюсь',
            self::WATCHED => 'Переглянуто',
        };
    }

    public static function labels(): array
    {
        return [
            self::FAVORITE->value => 'Улюблене',
            self::NOT_WATCHING->value => 'Не дивлюся',
            self::WATCHING->value => 'Дивлюся',
            self::PLANNED->value => 'В планах',
            self::STOPPED->value => 'Перестав',
            self::REWATCHING->value => 'Передивляюсь',
            self::WATCHED->value => 'Переглянуто',
        ];
    }
}
