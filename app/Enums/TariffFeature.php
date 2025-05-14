<?php

namespace AnimeSite\Enums;

enum TariffFeature: string
{
    case NO_ADS = 'no_ads';
    case PREMIUM_CONTENT = 'premium_content';
    case QUALITY_4K = 'quality_4k';
    case QUALITY_HD = 'quality_hd';
    case QUALITY_SD = 'quality_sd';
    case MULTIPLE_DEVICES = 'multiple_devices';
    case OFFLINE_VIEWING = 'offline_viewing';
    case EARLY_ACCESS = 'early_access';
    case PRIORITY_SUPPORT = 'priority_support';
    case EXCLUSIVE_EVENTS = 'exclusive_events';
    case FAMILY_SHARING = 'family_sharing';
    case UNLIMITED_VIEWING = 'unlimited_viewing';

    public function name(): string
    {
        return match ($this) {
            self::NO_ADS => 'Без реклами',
            self::PREMIUM_CONTENT => 'Доступ до преміального контенту',
            self::QUALITY_4K => '4K якість',
            self::QUALITY_HD => 'HD якість',
            self::QUALITY_SD => 'SD якість',
            self::MULTIPLE_DEVICES => 'Перегляд на декількох пристроях',
            self::OFFLINE_VIEWING => 'Завантаження для офлайн-перегляду',
            self::EARLY_ACCESS => 'Ранній доступ до нових релізів',
            self::PRIORITY_SUPPORT => 'Пріоритетна підтримка',
            self::EXCLUSIVE_EVENTS => 'Доступ до ексклюзивних подій',
            self::FAMILY_SHARING => 'Сімейний доступ',
            self::UNLIMITED_VIEWING => 'Необмежений перегляд',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::NO_ADS => 'Перегляд без рекламних вставок',
            self::PREMIUM_CONTENT => 'Доступ до ексклюзивного преміального контенту',
            self::QUALITY_4K => 'Перегляд у найвищій якості 4K Ultra HD',
            self::QUALITY_HD => 'Перегляд у високій якості Full HD',
            self::QUALITY_SD => 'Перегляд у стандартній якості',
            self::MULTIPLE_DEVICES => 'Можливість перегляду на декількох пристроях одночасно',
            self::OFFLINE_VIEWING => 'Завантаження аніме для перегляду без інтернету',
            self::EARLY_ACCESS => 'Доступ до нових серій раніше за інших',
            self::PRIORITY_SUPPORT => 'Першочергова підтримка від служби підтримки',
            self::EXCLUSIVE_EVENTS => 'Запрошення на ексклюзивні онлайн-події',
            self::FAMILY_SHARING => 'Можливість ділитися підпискою з членами родини',
            self::UNLIMITED_VIEWING => 'Необмежений час перегляду без обмежень',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($feature) => [$feature->value => $feature->name()])->toArray();
    }

    public static function toArray(): array
    {
        return collect(self::cases())->map(fn ($feature) => $feature->value)->toArray();
    }
}
