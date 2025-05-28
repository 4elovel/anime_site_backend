<?php

namespace AnimeSite\Enums;

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
    case ARABIC = 'ar';
    case PORTUGUESE = 'pt';
    case DUTCH = 'nl';

    /**
     * Get the language name by the code.
     */
    public function getName(): string
    {
        return match($this) {
            self::ENGLISH => 'Англійська',
            self::UKRAINIAN => 'Українська',
            self::FRENCH => 'Французька',
            self::GERMAN => 'Німецька',
            self::SPANISH => 'Іспанська',
            self::ITALIAN => 'Італійська',
            self::CHINESE => 'Китайська',
            self::JAPANESE => 'Японська',
            self::RUSSIAN => 'Російська',
            self::POLISH => 'Польська',
            self::ARABIC => 'Арабська',
            self::PORTUGUESE => 'Португальська',
            self::DUTCH => 'Нідерландська',
        };
    }

    public static function labels(): array
    {
        return [
            self::ENGLISH->value => 'English',
            self::UKRAINIAN->value => 'Ukrainian',
            self::FRENCH->value => 'French',
            self::GERMAN->value => 'German',
            self::SPANISH->value => 'Spanish',
            self::ITALIAN->value => 'Italian',
            self::CHINESE->value => 'Chinese',
            self::JAPANESE->value => 'Japanese',
            self::RUSSIAN->value => 'Russian',
            self::POLISH->value => 'Polish',
            self::ARABIC->value => 'Arabic',
            self::PORTUGUESE->value => 'Portuguese',
            self::DUTCH->value => 'Dutch',
        ];
    }

    /**
     * Get all language codes as options for select fields
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $language) => [
            $language->value => $language->getName()
        ])->toArray();
    }

    /**
     * Get all language codes as values
     */
    public static function values(): array
    {
        return collect(self::cases())->map(fn (self $language) => $language->value)->toArray();
    }

}
