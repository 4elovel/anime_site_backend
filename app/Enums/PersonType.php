<?php

namespace AnimeSite\Enums;

enum PersonType: string
{
    case CHARACTER = 'character';
    case VOICE_ACTOR = 'Voice Actor'; // Актор озвучення
    case DIRECTOR = 'Director'; // Режисер
    case PRODUCER = 'Producer'; // Продюсер
    case SCRIPTWRITER = 'Scriptwriter'; // Сценарист
    case CHARACTER_DESIGNER = 'Character Designer'; // Дизайнер персонажів
    case ANIMATION_DIRECTOR = 'Animation Director'; // Директор анімації
    case KEY_ANIMATOR = 'Key Animator'; // Ключовий аніматор
    case INBETWEEN_ANIMATOR = 'Inbetween Animator'; // Проміжний аніматор
    case BACKGROUND_ARTIST = 'Background Artist'; // Художник фону
    case COLOR_DESIGNER = 'Color Designer'; // Дизайнер кольору
    case SOUND_DIRECTOR = 'Sound Director'; // Звуковий режисер
    case MUSIC_COMPOSER = 'Music Composer'; // Композитор
    case EDITOR = 'Editor'; // Монтажер
    case CGI_ARTIST = 'CGI Artist'; // CGI-художник

    public function name(): string
    {
        return match ($this) {
            self::CHARACTER => 'Персонаж',
            self::VOICE_ACTOR => 'Актор озвучення',
            self::DIRECTOR => 'Режисер',
            self::PRODUCER => 'Продюсер',
            self::SCRIPTWRITER => 'Сценарист',
            self::CHARACTER_DESIGNER => 'Дизайнер персонажів',
            self::ANIMATION_DIRECTOR => 'Директор анімації',
            self::KEY_ANIMATOR => 'Ключовий аніматор',
            self::INBETWEEN_ANIMATOR => 'Проміжний аніматор',
            self::BACKGROUND_ARTIST => 'Художник фону',
            self::COLOR_DESIGNER => 'Дизайнер кольору',
            self::SOUND_DIRECTOR => 'Звуковий режисер',
            self::MUSIC_COMPOSER => 'Композитор',
            self::EDITOR => 'Монтажер',
            self::CGI_ARTIST => 'CGI-художник',
        };
    }

    public static function options(): array
    {
        return [
            self::CHARACTER->value => self::CHARACTER->name(),
            self::DIRECTOR->value => self::DIRECTOR->name(),
            self::PRODUCER->value => self::PRODUCER->name(),
            self::SCRIPTWRITER->value => self::SCRIPTWRITER->name(),
            self::CHARACTER_DESIGNER->value => self::CHARACTER_DESIGNER->name(),
            self::ANIMATION_DIRECTOR->value => self::ANIMATION_DIRECTOR->name(),
            self::KEY_ANIMATOR->value => self::KEY_ANIMATOR->name(),
            self::INBETWEEN_ANIMATOR->value => self::INBETWEEN_ANIMATOR->name(),
            self::BACKGROUND_ARTIST->value => self::BACKGROUND_ARTIST->name(),
            self::COLOR_DESIGNER->value => self::COLOR_DESIGNER->name(),
            self::SOUND_DIRECTOR->value => self::SOUND_DIRECTOR->name(),
            self::MUSIC_COMPOSER->value => self::MUSIC_COMPOSER->name(),
            self::VOICE_ACTOR->value => self::VOICE_ACTOR->name(),
            self::EDITOR->value => self::EDITOR->name(),
            self::CGI_ARTIST->value => self::CGI_ARTIST->name(),
        ];
    }

    public function getBadgeColor(): string
    {
        return match ($this) {
            self::CHARACTER => 'danger',
            self::VOICE_ACTOR => 'info',
            self::DIRECTOR => 'danger',
            self::PRODUCER => 'warning',
            self::SCRIPTWRITER => 'warning',
            self::CHARACTER_DESIGNER => 'success',
            self::ANIMATION_DIRECTOR => 'danger',
            self::KEY_ANIMATOR => 'primary',
            self::INBETWEEN_ANIMATOR => 'info',
            self::BACKGROUND_ARTIST => 'warning',
            self::COLOR_DESIGNER => 'success',
            self::SOUND_DIRECTOR => 'warning',
            self::MUSIC_COMPOSER => 'info',
            self::EDITOR => 'warning',
            self::CGI_ARTIST => 'primary',
        };
    }
}
