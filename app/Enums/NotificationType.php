<?php

namespace Liamtseva\Cinema\Enums;

enum NotificationType: string
{
    case NEW_EPISODE = 'new_episode';
    case ANIME_UPDATE = 'anime_update';
    case SYSTEM = 'system';
    case COMMENT_REPLY = 'comment_reply';

    public static function all(): array
    {
        return [
            self::NEW_EPISODE->value => 'Новий епізод',
            self::ANIME_UPDATE->value => 'Оновлення аніме',
            self::SYSTEM->value => 'Системне повідомлення',
            self::COMMENT_REPLY->value => 'Відповідь на коментар',
        ];
    }

    public function label(): string
    {
        return match($this) {
            self::NEW_EPISODE => 'Новий епізод',
            self::ANIME_UPDATE => 'Оновлення аніме',
            self::SYSTEM => 'Системне повідомлення',
            self::COMMENT_REPLY => 'Відповідь на коментар',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::NEW_EPISODE => 'heroicon-o-play',
            self::ANIME_UPDATE => 'heroicon-o-film',
            self::SYSTEM => 'heroicon-o-cog',
            self::COMMENT_REPLY => 'heroicon-o-chat-bubble-left',
        };
    }
}
