<?php

namespace AnimeSite\ValueObjects;


use AnimeSite\Enums\VideoPlayerName;
use AnimeSite\Enums\VideoQuality;


class VideoPlayer
{
    public function __construct(
        public VideoPlayerName $name,
        public string $url,
        public string $file_url,
        public string $dubbing,
        public VideoQuality $quality,
        public string $locale_code
    ) {}

}
