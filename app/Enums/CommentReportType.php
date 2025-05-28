<?php

namespace AnimeSite\Enums;

enum CommentReportType: string
{
    case INSULT = 'осквернення користувачів';
    case FLOOD_OFFTOP_MEANINGLESS = 'флуд / оффтоп / коментар без змісту';
    case AD_SPAM = 'реклама / спам';
    case SPOILER = 'спойлер';
    case PROVOCATION_CONFLICT = 'провокації / конфлікти';
    case INAPPROPRIATE_LANGUAGE = 'ненормативна лексика';
    case FORBIDDEN_UNNECESSARY_CONTENT = 'заборонений / непотрібний контент';
    case MEANINGLESS_EMPTY_TOPIC = 'безглузда / порожня тема';
    case DUPLICATE_TOPIC = 'дублікат теми';

    public static function labels(): array
    {
        return [
            self::INSULT->value => 'Осквернення користувачів',
            self::FLOOD_OFFTOP_MEANINGLESS->value => 'Флуд / оффтоп / коментар без змісту',
            self::AD_SPAM->value => 'Реклама / спам',
            self::SPOILER->value => 'Спойлер',
            self::PROVOCATION_CONFLICT->value => 'Провокації / конфлікти',
            self::INAPPROPRIATE_LANGUAGE->value => 'Ненормативна лексика',
            self::FORBIDDEN_UNNECESSARY_CONTENT->value => 'Заборонений / непотрібний контент',
            self::MEANINGLESS_EMPTY_TOPIC->value => 'Безглузда / порожня тема',
            self::DUPLICATE_TOPIC->value => 'Дублікат теми',
        ];
    }

    public function name(): string
    {
        return match ($this) {
            self::INSULT => 'Осквернення користувачів',
            self::FLOOD_OFFTOP_MEANINGLESS => 'Флуд / оффтоп / коментар без змісту',
            self::AD_SPAM => 'Реклама / спам',
            self::SPOILER => 'Спойлер',
            self::PROVOCATION_CONFLICT => 'Провокації / конфлікти',
            self::INAPPROPRIATE_LANGUAGE => 'Ненормативна лексика',
            self::FORBIDDEN_UNNECESSARY_CONTENT => 'Заборонений / непотрібний контент',
            self::MEANINGLESS_EMPTY_TOPIC => 'Безглузда / порожня тема',
            self::DUPLICATE_TOPIC => 'Дублікат теми',
        };
    }

    public function getBadgeColor(): string
    {
        return match ($this) {
            self::INSULT => 'danger',
            self::FLOOD_OFFTOP_MEANINGLESS => 'warning',
            self::AD_SPAM => 'info',
            self::SPOILER => 'primary',
            self::PROVOCATION_CONFLICT => 'danger',
            self::INAPPROPRIATE_LANGUAGE => 'danger',
            self::FORBIDDEN_UNNECESSARY_CONTENT => 'info',
            self::MEANINGLESS_EMPTY_TOPIC => 'info',
            self::DUPLICATE_TOPIC => 'warning',
        };
    }
}
