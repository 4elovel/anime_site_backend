<?php

namespace AnimeSite\Enums;

use AnimeSite\Enums\LanguageCode;

enum Country: string
{
    case EN_US = 'United States';
    case EN_GB = 'United Kingdom';
    case EN_AU = 'Australia';
    case EN_CA = 'Canada';
    case FR_FR = 'France';
    case DE_DE = 'Germany';
    case ES_ES = 'Spain';
    case ES_MX = 'Mexico';
    case IT_IT = 'Italy';
    case RU_RU = 'Russia';
    case UK_UA = 'Ukraine';
    case PL_PL = 'Poland';
    case JA_JP = 'Japan';
    case ZH_CN = 'China';
    case ZH_TW = 'Taiwan';
    case AR_SA = 'Saudi Arabia';
    case PT_PT = 'Portugal';
    case PT_BR = 'Brazil';
    case NL_NL = 'Netherlands';

    public function name(): string
    {
        return match ($this) {
            self::EN_US => 'США',
            self::EN_GB => 'Велика Британія',
            self::EN_AU => 'Австралія',
            self::EN_CA => 'Канада',
            self::FR_FR => 'Франція',
            self::DE_DE => 'Німеччина',
            self::ES_ES => 'Іспанія',
            self::ES_MX => 'Мексика',
            self::IT_IT => 'Італія',
            self::RU_RU => 'Росія',
            self::UK_UA => 'Україна',
            self::PL_PL => 'Польща',
            self::JA_JP => 'Японія',
            self::ZH_CN => 'Китай',
            self::ZH_TW => 'Тайвань',
            self::AR_SA => 'Саудівська Аравія',
            self::PT_PT => 'Португалія',
            self::PT_BR => 'Бразилія',
            self::NL_NL => 'Нідерланди',
        };
    }
    public static function labels(): array
    {
        return [
            self::EN_US->value => 'США',
            self::EN_GB->value => 'Велика Британія',
            self::EN_AU->value => 'Австралія',
            self::EN_CA->value => 'Канада',
            self::FR_FR->value => 'Франція',
            self::DE_DE->value => 'Німеччина',
            self::ES_ES->value => 'Іспанія',
            self::ES_MX->value => 'Мексика',
            self::IT_IT->value => 'Італія',
            self::RU_RU->value => 'Росія',
            self::UK_UA->value => 'Україна',
            self::PL_PL->value => 'Польща',
            self::JA_JP->value => 'Японія',
            self::ZH_CN->value => 'Китай',
            self::ZH_TW->value => 'Тайвань',
            self::AR_SA->value => 'Саудівська Аравія',
            self::PT_PT->value => 'Португалія',
            self::PT_BR->value => 'Бразилія',
            self::NL_NL->value => 'Нідерланди',
        ];
    }

    public function languageCode(): string
    {
        return match ($this) {
            self::EN_US, self::EN_GB, self::EN_AU, self::EN_CA => LanguageCode::ENGLISH->value,
            self::FR_FR => LanguageCode::FRENCH->value,
            self::DE_DE => LanguageCode::GERMAN->value,
            self::ES_ES, self::ES_MX => LanguageCode::SPANISH->value,
            self::IT_IT => LanguageCode::ITALIAN->value,
            self::RU_RU => LanguageCode::RUSSIAN->value,
            self::UK_UA => LanguageCode::UKRAINIAN->value,
            self::PL_PL => LanguageCode::POLISH->value,
            self::JA_JP => LanguageCode::JAPANESE->value,
            self::ZH_CN, self::ZH_TW => LanguageCode::CHINESE->value,
            self::AR_SA => LanguageCode::ARABIC->value,
            self::PT_PT, self::PT_BR => LanguageCode::PORTUGUESE->value,
            self::NL_NL => LanguageCode::DUTCH->value,
        };
    }

    public static function languageCodes(): array
    {
        return [
            self::EN_US->value => LanguageCode::ENGLISH->value,
            self::FR_FR->value => LanguageCode::FRENCH->value,
            self::DE_DE->value => LanguageCode::GERMAN->value,
            self::ES_ES->value => LanguageCode::SPANISH->value,
            self::IT_IT->value => LanguageCode::ITALIAN->value,
            self::RU_RU->value => LanguageCode::RUSSIAN->value,
            self::UK_UA->value => LanguageCode::UKRAINIAN->value,
            self::PL_PL->value => LanguageCode::POLISH->value,
            self::JA_JP->value => LanguageCode::JAPANESE->value,
            self::ZH_CN->value => LanguageCode::CHINESE->value,
            self::AR_SA->value => LanguageCode::ARABIC->value,
            self::PT_PT->value => LanguageCode::PORTUGUESE->value,
            self::NL_NL->value => LanguageCode::DUTCH->value,
        ];
    }

    public function description(): string
    {
        return match ($this) {
            self::EN_US => 'США — країна в Північній Америці, одна з найбільших економік світу та лідер в технологічних інноваціях.',
            self::EN_GB => 'Велика Британія — країна в Західній Європі, відома своєю історією, культурою та королівською родиною.',
            self::EN_AU => 'Австралія — країна на південному заході Тихого океану, відома своїми природними ресурсами та унікальною фауною.',
            self::EN_CA => 'Канада — країна в Північній Америці, відома своєю величезною природною красою та високим рівнем життя.',
            self::FR_FR => 'Франція — країна в Західній Європі, відома своєю культурною спадщиною, вином та мистецтвом.',
            self::DE_DE => 'Німеччина — країна в Центральній Європі, одна з найбільших економік світу.',
            self::ES_ES => 'Іспанія — країна в Південній Європі, відома своєю культурою, архітектурою та кухнею.',
            self::ES_MX => 'Мексика — країна в Північній Америці, відома своєю культурною спадщиною та гастрономією.',
            self::IT_IT => 'Італія — країна в Південній Європі, колиска Римської імперії, відома своєю кухнею та мистецтвом.',
            self::RU_RU => 'Росія — найбільша країна у світі за площею, що займає значну частину Євразії.',
            self::UK_UA => 'Україна — велика країна у Східній Європі, з багатою історією та культурною спадщиною.',
            self::PL_PL => 'Польща — країна в Центральній Європі з багатою історією та культурною спадщиною.',
            self::JA_JP => 'Японія — острівна країна в Східній Азії, відома своєю технологією, культурою та унікальними традиціями.',
            self::ZH_CN => 'Китай — найбільша країна за чисельністю населення та одна з провідних економік світу.',
            self::ZH_TW => 'Тайвань — острівна країна в Східній Азії, що має складні відносини з Китаєм.',
            self::AR_SA => 'Саудівська Аравія — країна на Близькому Сході, відома нафтовими запасами та релігійним значенням для мусульман.',
            self::PT_PT => 'Португалія — країна на південному заході Європи, відома своєю історією та культурною спадщиною.',
            self::PT_BR => 'Бразилія — країна в Південній Америці, найбільша країна португаломовного світу.',
            self::NL_NL => 'Нідерланди — країна в Західній Європі, відома своїми каналами, вітряками та тюльпанами.',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::EN_US => '/icons/countries/us.png',
            self::EN_GB => '/icons/countries/gb.png',
            self::EN_AU => '/icons/countries/au.png',
            self::EN_CA => '/icons/countries/ca.png',
            self::FR_FR => '/icons/countries/fr.png',
            self::DE_DE => '/icons/countries/de.png',
            self::ES_ES => '/icons/countries/es.png',
            self::ES_MX => '/icons/countries/mx.png',
            self::IT_IT => '/icons/countries/it.png',
            self::RU_RU => '/icons/countries/ru.png',
            self::UK_UA => '/icons/countries/ua.png',
            self::PL_PL => '/icons/countries/pl.png',
            self::JA_JP => '/icons/countries/jp.png',
            self::ZH_CN => '/icons/countries/cn.png',
            self::ZH_TW => '/icons/countries/tw.png',
            self::AR_SA => '/icons/countries/sa.png',
            self::PT_PT => '/icons/countries/pt.png',
            self::PT_BR => '/icons/countries/br.png',
            self::NL_NL => '/icons/countries/nl.png',
        };
    }

    public function metaTitle(): string
    {
        return match ($this) {
            self::EN_US => 'Аніме зі США',
            self::EN_GB => 'Аніме з Великої Британії',
            self::EN_AU => 'Аніме з Австралії',
            self::EN_CA => 'Аніме з Канади',
            self::FR_FR => 'Аніме з Франції',
            self::DE_DE => 'Аніме з Німеччини',
            self::ES_ES => 'Аніме з Іспанії',
            self::ES_MX => 'Аніме з Мексики',
            self::IT_IT => 'Аніме з Італії',
            self::RU_RU => 'Аніме з Росії',
            self::UK_UA => 'Аніме з України',
            self::PL_PL => 'Аніме з Польщі',
            self::JA_JP => 'Аніме з Японії',
            self::ZH_CN => 'Аніме з Китаю',
            self::ZH_TW => 'Аніме з Тайваню',
            self::AR_SA => 'Аніме з Саудівської Аравії',
            self::PT_PT => 'Аніме з Португалії',
            self::PT_BR => 'Аніме з Бразилії',
            self::NL_NL => 'Аніме з Нідерландів',
        };
    }

    public function metaDescription(): string
    {
        return match ($this) {
            self::EN_US => 'США — країна в Північній Америці, одна з найбільших економік світу та лідер в технологічних інноваціях.',
            self::EN_GB => 'Велика Британія — країна в Західній Європі, відома своєю історією, культурою та королівською родиною.',
            self::EN_AU => 'Австралія — країна на південному заході Тихого океану, відома своїми природними ресурсами та унікальною фауною.',
            self::EN_CA => 'Канада — країна в Північній Америці, відома своєю величезною природною красою та високим рівнем життя.',
            self::FR_FR => 'Франція — країна в Західній Європі, відома своєю культурною спадщиною, вином та мистецтвом.',
            self::DE_DE => 'Німеччина — країна в Центральній Європі, одна з найбільших економік світу.',
            self::ES_ES => 'Іспанія — країна в Південній Європі, відома своєю культурою, архітектурою та кухнею.',
            self::ES_MX => 'Мексика — країна в Північній Америці, відома своєю культурною спадщиною та гастрономією.',
            self::IT_IT => 'Італія — країна в Південній Європі, колиска Римської імперії, відома своєю кухнею та мистецтвом.',
            self::RU_RU => 'Росія — найбільша країна у світі за площею, що займає значну частину Євразії.',
            self::UK_UA => 'Україна — велика країна у Східній Європі, з багатою історією та культурною спадщиною.',
            self::PL_PL => 'Польща — країна в Центральній Європі з багатою історією та культурною спадщиною.',
            self::JA_JP => 'Японія — острівна країна в Східній Азії, відома своєю технологією, культурою та унікальними традиціями.',
            self::ZH_CN => 'Китай — найбільша країна за чисельністю населення та одна з провідних економік світу.',
            self::ZH_TW => 'Тайвань — острівна країна в Східній Азії, що має складні відносини з Китаєм.',
            self::AR_SA => 'Саудівська Аравія — країна на Близькому Сході, відома нафтовими запасами та релігійним значенням для мусульман.',
            self::PT_PT => 'Португалія — країна на південному заході Європи, відома своєю історією та культурною спадщиною.',
            self::PT_BR => 'Бразилія — країна в Південній Америці, найбільша країна португаломовного світу.',
            self::NL_NL => 'Нідерланди — країна в Західній Європі, відома своїми каналами, вітряками та тюльпанами.',
        };
    }

    public static function toArray(): array
    {
        return [
            self::EN_US->value => self::EN_US->name(),
            self::EN_GB->value => self::EN_GB->name(),
            self::EN_AU->value => self::EN_AU->name(),
            self::EN_CA->value => self::EN_CA->name(),
            self::FR_FR->value => self::FR_FR->name(),
            self::DE_DE->value => self::DE_DE->name(),
            self::ES_ES->value => self::ES_ES->name(),
            self::ES_MX->value => self::ES_MX->name(),
            self::IT_IT->value => self::IT_IT->name(),
            self::RU_RU->value => self::RU_RU->name(),
            self::UK_UA->value => self::UK_UA->name(),
            self::PL_PL->value => self::PL_PL->name(),
            self::JA_JP->value => self::JA_JP->name(),
            self::ZH_CN->value => self::ZH_CN->name(),
            self::ZH_TW->value => self::ZH_TW->name(),
            self::AR_SA->value => self::AR_SA->name(),
            self::PT_PT->value => self::PT_PT->name(),
            self::PT_BR->value => self::PT_BR->name(),
            self::NL_NL->value => self::NL_NL->name(),
        ];
    }

    private function getCountryName(string $code): string
    {
        return match ($code) {
            'EN_US' => 'США',
            'EN_GB' => 'Велика Британія',
            'EN_AU' => 'Австралія',
            'EN_CA' => 'Канада',
            'FR_FR' => 'Франція',
            'DE_DE' => 'Німеччина',
            'ES_ES' => 'Іспанія',
            'ES_MX' => 'Мексика',
            'IT_IT' => 'Італія',
            'RU_RU' => 'Росія',
            'UK_UA' => 'Україна',
            'PL_PL' => 'Польща',
            'JA_JP' => 'Японія',
            'ZH_CN' => 'Китай',
            'ZH_TW' => 'Тайвань',
            'AR_SA' => 'Саудівська Аравія',
            'PT_PT' => 'Португалія',
            'PT_BR' => 'Бразилія',
            'NL_NL' => 'Нідерланди',
            default => $code, // Якщо код не знайдений, повертаємо сам код
        };
    }

}
