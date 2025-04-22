<?php

namespace Liamtseva\Cinema\Enums;

enum Kind: string
{
    case TV_SERIES = 'tv_series';
    case TV_SPECIAL = 'tv_special';
    case FULL_LENGTH = 'full_length';
    case SHORT_FILM = 'short_film';
    case OVA = 'ova';
    case ONA = 'ona';

    public function name(): string
    {
        return match ($this) {
            self::TV_SERIES => 'Аніме серіал',
            self::TV_SPECIAL => 'Спеціальний випуск аніме',
            self::FULL_LENGTH => 'Повнометражний аніме фільм',
            self::SHORT_FILM => 'Короткометражний аніме фільм',
            self::OVA => 'Оригінальна відео-анімація (OVA)',
            self::ONA => 'Оригінальна інтернет-анімація (ONA)',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::TV_SERIES => 'Аніме серіал, що складається з кількох сезонів, з епізодами, які розвивають основну сюжетну лінію.',
            self::TV_SPECIAL => 'Спеціальні аніме серії, які є додатковими епізодами до основного серіалу або мають окрему сюжетну лінію.',
            self::FULL_LENGTH => 'Повнометражний аніме фільм, що має довжину від 1 до кількох годин.',
            self::SHORT_FILM => 'Короткометражний аніме фільм, який зазвичай має тривалість від кількох хвилин до 30 хвилин.',
            self::OVA => 'Оригінальні відео-анімації (OVA), що зазвичай являють собою додаткові епізоди або окремі історії для фанатів.',
            self::ONA => 'Інтернет-анімація (ONA), яка доступна виключно через онлайн платформи та не транслюється по телевізору.',
        };
    }


    public function metaTitle(): string
    {
        return match ($this) {
            self::TV_SERIES => 'Аніме серіали онлайн | Анімепортал',
            self::TV_SPECIAL => 'Аніме спеціальні серії онлайн | Анімепортал',
            self::FULL_LENGTH => 'Повнометражні аніме фільми онлайн | Анімепортал',
            self::SHORT_FILM => 'Короткометражні аніме фільми онлайн | Анімепортал',
            self::OVA => 'OVA онлайн | Анімепортал',
            self::ONA => 'ONA онлайн | Анімепортал',
        };
    }


    public function metaDescription(): string
    {
        return match ($this) {
            self::TV_SERIES => 'Ознайомтеся з найкращими аніме серіалами онлайн, від класичних до нових тайтлів.',
            self::TV_SPECIAL => 'Дивіться спеціальні епізоди аніме серіалів, що не входять в основний сюжет.',
            self::FULL_LENGTH => 'Перегляньте повнометражні аніме фільми онлайн, від епічних до коротших історій.',
            self::SHORT_FILM => 'Дивіться короткометражні аніме фільми онлайн, що вражають сюжетом за короткий час.',
            self::OVA => 'Оригінальні відео-анімації (OVA) — ексклюзивні аніме серії для фанатів.',
            self::ONA => 'Інтернет-анімації (ONA) — аніме, яке доступне тільки онлайн, без традиційного телевізійного випуску.',
        };
    }



    public function metaImage(): string
    {
        return match ($this) {
            self::TV_SERIES => '/images/seo/tv-series.jpg',
            self::TV_SPECIAL => '/images/seo/tv-special.jpg',
            self::FULL_LENGTH => '/images/seo/full_length.jpg',
            self::SHORT_FILM => '/images/seo/short_film.jpg',
            self::OVA => '/images/seo/ova.jpg',
            self::ONA => '/images/seo/ona.jpg',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $kind) => [
                $kind->value => $kind->name(),
            ])
            ->toArray();
    }
}
