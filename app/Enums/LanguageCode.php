<?php

namespace Liamtseva\Cinema\Enums;

enum LanguageCode: string
{
    case ENGLISH = 'en';
    case UKRAINIAN = 'uk';
    case FRENCH = 'fr';
    case GERMAN = 'de';
    case SPANISH = 'es';
    case ITALIAN = 'it';
    case CHINESE = 'zh';
    case JAPANESE = 'ja';
    case RUSSIAN = 'ru';
    case POLISH = 'pl';

    /**
     * Get the language name by the code.
     */
    public function getName(): string
    {
        return match($this) {
            self::ENGLISH => 'English',
            self::UKRAINIAN => 'Ukrainian',
            self::FRENCH => 'French',
            self::GERMAN => 'German',
            self::SPANISH => 'Spanish',
            self::ITALIAN => 'Italian',
            self::CHINESE => 'Chinese',
            self::JAPANESE => 'Japanese',
            self::RUSSIAN => 'Russian',
            self::POLISH => 'Polish',
        };
    }

    /**
     * Get all languages as an array of codes and names.
     */
    public static function all(): array
    {
        return array_map(fn($case) => [
            'code' => $case->value,
            'name' => $case->getName(),
        ], self::cases());
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'name' => $case->getName(),
        ], self::cases());
    }
}
