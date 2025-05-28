<?php

namespace AnimeSite\Enums;

enum NotificationType: string
{
    case ACHIEVEMENTS = 'Ви отримали нове досягнення!';
    case NEW_EPISODE = 'Вийшла нова серія!';
    case NEW_ANIME = 'Додано нове аніме до каталогу!';
    case RECOMMENDATION = 'Ми знайшли для вас нове аніме, яке може сподобатися!';
    case SUBSCRIPTION = 'Підписка на вашого улюбленого автора оновлена!';
    case ANIME_REMINDER = 'Не забудьте переглянути новий епізод!';
    case POPULAR_ANIME = 'Ось топ популярних аніме цього тижня!';

    public function getName(): string
    {
        return $this->value;
    }

    /**
     * Отримати всі варіанти повідомлень як масив.
     */
    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

}
