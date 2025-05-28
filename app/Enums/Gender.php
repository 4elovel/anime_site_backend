<?php

namespace AnimeSite\Enums;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';

    public static function labels(): array
    {
        return [
            self::MALE->value => 'Чоловіча',
            self::FEMALE->value => 'Жіноча',
            self::OTHER->value => 'Інша',
        ];
    }
    public function name(): string
    {
        return match ($this) {
            self::MALE => 'Чоловіча',
            self::FEMALE => 'Жіноча',
            self::OTHER => 'Інша',
        };
    }

    public function getBadgeColor(): string
    {
        return match ($this) {
            self::MALE => 'info',
            self::FEMALE => 'danger',
            self::OTHER => 'success',
        };
    }
}
