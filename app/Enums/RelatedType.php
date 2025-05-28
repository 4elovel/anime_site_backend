<?php

namespace AnimeSite\Enums;

enum RelatedType : string
{
    case SEASON = 'season';
    case MOVIE = 'movie';
    case OVA = 'ova';
    case ONA = 'ona';
    case SPECIAL = 'special';

    public static function labels(): array
    {
        return [
            self::SEASON->value => 'Сезон',
            self::MOVIE->value => 'Фільм',
            self::OVA->value => 'Тизер',
            self::SPECIAL->value => 'Спеціальний випуск аніме',
            self::OVA->value => 'Оригінальна відео-анімація (OVA)',
            self::ONA->value => 'Оригінальна інтернет-анімація (ONA)',
        ];
    }
}
