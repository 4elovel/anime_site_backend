<?php

namespace AnimeSite\Enums;

enum Source: string
{
    case DORAMA = 'dorama';
    case MANGA = 'manga';
    case GAME = 'game';
    case NOVEL = 'novel';
    case COMIC = 'comic';
    case LIGHT_NOVEL = 'light_novel';
    case WEBTOON = 'webtoon';

    public function name(): string
    {
        return match ($this) {
            self::DORAMA => 'Дорама',
            self::MANGA => 'Манга',
            self::GAME => 'Гра',
            self::NOVEL => 'Роман',
            self::COMIC => 'Комікс',
            self::LIGHT_NOVEL => 'Лайт-новел',
            self::WEBTOON => 'Вебтун',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::DORAMA => 'Дорама — це популярний жанр японських або корейських телевізійних серіалів, які часто базуються на манзі чи романах.',
            self::MANGA => 'Манга — японські комікси, які часто стають основою для аніме або фільмів.',
            self::GAME => 'Гри, на основі яких створюються аніме, фільми або серіали, часто мають своїх унікальних персонажів та сюжет.',
            self::NOVEL => 'Роман — це літературний твір, який часто адаптується в інші формати, такі як фільми чи серіали.',
            self::COMIC => 'Комікси — це твори в жанрі малюнкової графіки, що можуть стати основою для адаптацій в кіно чи аніме.',
            self::LIGHT_NOVEL => 'Лайт-новели — японські короткі романи з ілюстраціями, часто адаптовані в аніме або мангу.',
            self::WEBTOON => 'Вебтуни — це цифрові комікси, що публікуються онлайн, часто мають захоплюючі сюжети та персонажів.',
        };
    }

    public function metaTitle(): string
    {
        return match ($this) {
            self::DORAMA => 'Дорама: Твори японської та корейської культури',
            self::MANGA => 'Манга: Сучасні японські комікси та їх екранізації',
            self::GAME => 'Гри, що стали основою для аніме',
            self::NOVEL => 'Романи, що адаптовані в аніме',
            self::COMIC => 'Комікси: Від класики до сучасних адаптацій',
            self::LIGHT_NOVEL => 'Лайт-новели: Від тексту до аніме',
            self::WEBTOON => 'Вебтуни: Цифрові комікси для сучасної аудиторії',
        };
    }

    public function metaDescription(): string
    {
        return match ($this) {
            self::DORAMA => 'Дорама — це японські та корейські телевізійні серіали, які здобули популярність завдяки емоційному сюжету та глибоким персонажам.',
            self::MANGA => 'Манга — це японські комікси, відомі своїм унікальним стилем та сюжетами, які часто адаптуються в аніме.',
            self::GAME => 'Гри часто стають основою для аніме, що дозволяє геймерам насолоджуватися своїми улюбленими персонажами та світом в інших форматах.',
            self::NOVEL => 'Романи часто перетворюються на фільми та серіали, надаючи нове життя літературним творам, що вражають читачів глибокими сюжетами.',
            self::COMIC => 'Комікси, від класичних до сучасних, стають основою для великих екранізацій, привертаючи увагу фанатів у всьому світі.',
            self::LIGHT_NOVEL => 'Лайт-новели — це короткі японські романи з ілюстраціями, які часто адаптовані в аніме або мангу, привертаючи увагу молодої аудиторії.',
            self::WEBTOON => 'Вебтуни — це цифрові комікси, які публікуються онлайн, і мають великий вплив на молодіжну культуру та сучасний кінематограф.',
        };
    }

    public function metaImage(): string
    {
        return match ($this) {
            self::DORAMA => '/images/seo/dorama.jpg',
            self::MANGA => '/images/seo/manga.jpg',
            self::GAME => '/images/seo/game.jpg',
            self::NOVEL => '/images/seo/novel.jpg',
            self::COMIC => '/images/seo/comic.jpg',
            self::LIGHT_NOVEL => '/images/seo/light_novel.jpg',
            self::WEBTOON => '/images/seo/webtoon.jpg',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $source) => [$source->value => $source->name()])
            ->toArray();
    }

    public function getBadgeColor(): string
    {
        return match ($this) {
            self::DORAMA => 'info',
            self::MANGA => 'primary',
            self::GAME => 'success',
            self::NOVEL => 'warning',
            self::COMIC => 'danger',
            self::LIGHT_NOVEL => 'success',
            self::WEBTOON => 'danger',
        };
    }
}
