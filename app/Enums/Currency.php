<?php

namespace AnimeSite\Enums;

enum Currency : string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case UAH = 'UAH';
    case GBP = 'GBP';
    case JPY = 'JPY';

    public function label(): string
    {
        return match ($this) {
            self::USD => 'Долар США',
            self::EUR => 'Євро',
            self::UAH => 'Гривня',
            self::GBP => 'Фунт стерлінгів',
            self::JPY => 'Японська єна',
        };
    }

    /**
     * Список валют для Select
     */
    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($currency) => [$currency->value => $currency->label()])
            ->toArray();
    }

    public function symbol(): string
    {
        return match ($this) {
            self::USD => '$',
            self::EUR => '€',
            self::UAH => '₴',
            self::GBP => '£',
            self::JPY => '¥',
        };
    }

}
