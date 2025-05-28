<?php

namespace AnimeSite\Enums;

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

    public function getBadgeColor(): string
    {
        return match ($this) {
            self::NOT_WATCHING => 'primary',
            self::STOPPED => 'secondary',
            self::WATCHED => 'warning',
            self::PLANNED => 'info',
            self::FAVORITE => 'danger',
            self::WATCHING => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::NOT_WATCHING => 'heroicon-s-no-symbol',
            self::STOPPED => 'heroicon-s-stop-circle',
            self::WATCHED => 'heroicon-s-check',
            self::PLANNED => 'heroicon-s-clock',
            self::FAVORITE => 'heroicon-s-heart',
            self::WATCHING => 'heroicon-s-computer-desktop',
        };
    }
}
