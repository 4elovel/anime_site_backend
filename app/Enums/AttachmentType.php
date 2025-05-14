<?php

namespace AnimeSite\Enums;

enum AttachmentType: string
{
    case PICTURE = 'picture'; // Зображення
    case TRAILER = 'trailer'; // Трейлер
    case TEASER = 'teaser'; // Тизер
    case CLIP = 'clip'; // Кліп
    case BEHIND_THE_SCENES = 'behind_the_scenes'; // За лаштунками
    case BAD_TAKES = 'bad_takes'; // Невдалі дублі
    case SHORT_FILMS = 'short_films'; // Короткометражні фільми



    public static function labels(): array
    {
        return [
            self::PICTURE->value => 'Зображення',
            self::TRAILER->value => 'Трейлер',
            self::TEASER->value => 'Тизер',
            self::CLIP->value => 'Кліп',
            self::BEHIND_THE_SCENES->value => 'За лаштунками',
            self::BAD_TAKES->value => 'Невдалі дублі',
            self::SHORT_FILMS->value => 'Короткометражні фільми',
        ];
    }
}
