<?php

namespace AnimeSite\Enums;

enum Status: string
{
    case ANONS = 'anons';
    case ONGOING = 'ongoing';
    case RELEASED = 'released';
    case CANCELED = 'canceled';
    case RUMORED = 'rumored';

    public function name(): string
    {
        return match ($this) {
            self::ANONS => 'Анонс',
            self::ONGOING => 'У процесі',
            self::RELEASED => 'Випущено',
            self::CANCELED => 'Скасовано',
            self::RUMORED => 'Чутки',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ANONS => 'Нові аніме, які скоро з\'являться на екранах.',
            self::ONGOING => 'Аніме, що зараз показуються або випускаються епізодами.',
            self::RELEASED => 'Аніме, які вже доступні до перегляду.',
            self::CANCELED => 'Проекти, які були скасовані і більше не будуть випущені.',
            self::RUMORED => 'Проекти, які знаходяться на стадії чуток і ще не були офіційно анонсовані.',
        };
    }

    public function metaTitle(): string
    {
        return match ($this) {
            self::ANONS => 'Анонс нових аніме | Анімепортал',
            self::ONGOING => 'Аніме у процесі показу | Анімепортал',
            self::RELEASED => 'Доступні аніме | Анімепортал',
            self::CANCELED => 'Скасовані проекти | Анімепортал',
            self::RUMORED => 'Чутки про майбутні проекти | Анімепортал',
        };
    }

    public function metaDescription(): string
    {
        return match ($this) {
            self::ANONS => 'Дізнайтеся про нові анонси аніме, які скоро будуть доступні на великому екрані.',
            self::ONGOING => 'Ознайомтеся з аніме, які зараз виходять, і залишайтеся в курсі нових епізодів.',
            self::RELEASED => 'Перегляньте всі доступні аніме, що вже випущені для перегляду.',
            self::CANCELED => 'Дізнайтеся про аніме, які були скасовані і більше не будуть випущені.',
            self::RUMORED => 'Перегляньте аніме, які зараз вважаються чутками і ще не підтверджені.',
        };
    }

    public function metaImage(): string
    {
        return match ($this) {
            self::ANONS => '/images/seo/anons-anime.jpg',
            self::ONGOING => '/images/seo/ongoing-anime.jpg',
            self::RELEASED => '/images/seo/released-anime.jpg',
            self::CANCELED => '/images/seo/canceled-projects.jpg',
            self::RUMORED => '/images/seo/rumored-projects.jpg',
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
            self::ANONS => 'info',
            self::ONGOING => 'primary',
            self::RELEASED => 'success',
            self::CANCELED => 'danger',
            self::RUMORED => 'warning',
        };
    }

}
