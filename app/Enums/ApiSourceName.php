<?php

namespace AnimeSite\Enums;

enum ApiSourceName: string
{
    case TMDB = 'tmdb';
    case SHIKI = 'shiki';
    case IMDB = 'imdb';
    case ANILIST = 'anilist';

    public static function labels(): array
    {
        return [
            self::TMDB->value => 'tmdb',
            self::SHIKI->value => 'shiki',
            self::IMDB->value => 'imdb',
            self::ANILIST->value => 'anilist',
        ];
    }
}
