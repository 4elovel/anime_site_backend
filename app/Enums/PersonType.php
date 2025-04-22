<?php

namespace Liamtseva\Cinema\Enums;

enum PersonType: string
{
    case CHARACTER = 'character';
    case DIRECTOR = 'director';
    case PRODUCER = 'producer';
    case WRITER = 'writer';
    case EDITOR = 'editor';
    case CINEMATOGRAPHER = 'cinematographer';
    case COMPOSER = 'composer';
    case ART_DIRECTOR = 'art_director';
    case SOUND_DESIGNER = 'sound_designer';
    case MAKEUP_ARTIST = 'makeup_artist';
    case VOICE_ACTOR = 'voice_actor';
    case STUNT_PERFORMER = 'stunt_performer';
    case ASSISTANT_DIRECTOR = 'assistant_director';
    case PRODUCER_ASSISTANT = 'producer_assistant';
    case SCRIPT_SUPERVISOR = 'script_supervisor';
    case PRODUCTION_DESIGNER = 'production_designer';
    case VISUAL_EFFECTS_SUPERVISOR = 'visual_effects_supervisor';

    public function name(): string
    {
        return match ($this) {
            self::CHARACTER => 'Персонаж',
            self::DIRECTOR => 'Режисер',
            self::PRODUCER => 'Продюсер',
            self::WRITER => 'Сценарист',
            self::EDITOR => 'Монтажер',
            self::CINEMATOGRAPHER => 'Оператор',
            self::COMPOSER => 'Композитор',
            self::ART_DIRECTOR => 'Художник-постановник',
            self::SOUND_DESIGNER => 'Звуковий дизайнер',
            self::MAKEUP_ARTIST => 'Візажист',
            self::VOICE_ACTOR => 'Актор дубляжу',
            self::STUNT_PERFORMER => 'Каскадер',
            self::ASSISTANT_DIRECTOR => 'Помічник режисера',
            self::PRODUCER_ASSISTANT => 'Помічник продюсера',
            self::SCRIPT_SUPERVISOR => 'Супервайзер сценарію',
            self::PRODUCTION_DESIGNER => 'Продакшн-дизайнер',
            self::VISUAL_EFFECTS_SUPERVISOR => 'Супервайзер візуальних ефектів',
        };
    }

    public static function options(): array
    {
        return [
            self::CHARACTER->value => self::CHARACTER->name(),
            self::DIRECTOR->value => self::DIRECTOR->name(),
            self::PRODUCER->value => self::PRODUCER->name(),
            self::WRITER->value => self::WRITER->name(),
            self::EDITOR->value => self::EDITOR->name(),
            self::CINEMATOGRAPHER->value => self::CINEMATOGRAPHER->name(),
            self::COMPOSER->value => self::COMPOSER->name(),
            self::ART_DIRECTOR->value => self::ART_DIRECTOR->name(),
            self::SOUND_DESIGNER->value => self::SOUND_DESIGNER->name(),
            self::MAKEUP_ARTIST->value => self::MAKEUP_ARTIST->name(),
            self::VOICE_ACTOR->value => self::VOICE_ACTOR->name(),
            self::STUNT_PERFORMER->value => self::STUNT_PERFORMER->name(),
            self::ASSISTANT_DIRECTOR->value => self::ASSISTANT_DIRECTOR->name(),
            self::PRODUCER_ASSISTANT->value => self::PRODUCER_ASSISTANT->name(),
            self::SCRIPT_SUPERVISOR->value => self::SCRIPT_SUPERVISOR->name(),
            self::PRODUCTION_DESIGNER->value => self::PRODUCTION_DESIGNER->name(),
            self::VISUAL_EFFECTS_SUPERVISOR->value => self::VISUAL_EFFECTS_SUPERVISOR->name(),
        ];
    }
}
