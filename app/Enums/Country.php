<?php

namespace Liamtseva\Cinema\Enums;

enum Country: string
{
    case USA = 'us';
    case JAPAN = 'jp';
    case CHINA = 'cn';
    case FRANCE = 'fr';
    case INDIA = 'in';
    case CANADA = 'ca';
    case SOUTH_KOREA = 'kr';


    public function name(): string
    {
        return match ($this) {
            self::USA => 'США',
            self::JAPAN => 'Японія',
            self::CHINA => 'Китай',
            self::FRANCE => 'Франція',
            self::INDIA => 'Індія',
            self::CANADA => 'Канада',
            self::SOUTH_KOREA => 'Південна Корея',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::USA => 'США — країна в Північній Америці, одна з найбільших економік світу та лідер в технологічних інноваціях.',
            self::JAPAN => 'Японія — острівна країна в Східній Азії, відома своєю технологією, культурою та унікальними традиціями.',
            self::CHINA => 'Китай — найбільша країна за чисельністю населення та одна з провідних економік світу.',
            self::FRANCE => 'Франція — країна в Західній Європі, відома своєю культурною спадщиною, вином та мистецтвом.',
            self::INDIA => 'Індія — велика країна в Південній Азії, що має багатий культурний спадок та величезне населення.',
            self::CANADA => 'Канада — країна в Північній Америці, відома своєю величезною природною красою та високим рівнем життя.',
            self::SOUTH_KOREA => 'Південна Корея — технологічно розвинена країна в Східній Азії, відома своєю культурою та індустрією розваг.',

        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::USA => '/icons/countries/us.png',
            self::JAPAN => '/icons/countries/jp.png',
            self::CHINA => '/icons/countries/cn.png',
            self::FRANCE => '/icons/countries/fr.png',
            self::INDIA => '/icons/countries/in.png',
            self::CANADA => '/icons/countries/ca.png',
            self::SOUTH_KOREA => '/icons/countries/kr.png',

        };
    }

    public function metaTitle(): string
    {
        return match ($this) {
            self::USA => 'Аніме зі США',
            self::JAPAN => 'Аніме з Японії',
            self::CHINA => 'Аніме з Китаю',
            self::FRANCE => 'Аніме з Франції',
            self::INDIA => 'Аніме з Індії',
            self::CANADA => 'Аніме з Канади',
            self::SOUTH_KOREA => 'Аніме з Південної Кореї',

        };
    }

    public function metaDescription(): string
    {
        return match ($this) {
            self::USA => 'США — країна в Північній Америці, одна з найбільших економік світу та лідер в технологічних інноваціях.',
            self::JAPAN => 'Японія — острівна країна в Східній Азії, відома своєю технологією, культурою та унікальними традиціями.',
            self::CHINA => 'Китай — найбільша країна за чисельністю населення та одна з провідних економік світу.',
            self::FRANCE => 'Франція — країна в Західній Європі, відома своєю культурною спадщиною, вином та мистецтвом.',
            self::INDIA => 'Індія — велика країна в Південній Азії, що має багатий культурний спадок та величезне населення.',
            self::CANADA => 'Канада — країна в Північній Америці, відома своєю величезною природною красою та високим рівнем життя.',
            self::SOUTH_KOREA => 'Південна Корея — технологічно розвинена країна в Східній Азії, відома своєю культурою та індустрією розваг.',
        };
    }


    public static function toArray(): array
    {
        return [
            self::USA->value => self::USA->name(),
            self::JAPAN->value => self::JAPAN->name(),
            self::CHINA->value => self::CHINA->name(),
            self::FRANCE->value => self::FRANCE->name(),
            self::INDIA->value => self::INDIA->name(),
            self::CANADA->value => self::CANADA->name(),
            self::SOUTH_KOREA->value => self::SOUTH_KOREA->name(),
        ];
    }
}
