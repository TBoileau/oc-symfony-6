<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

enum VideoProvider: string
{
    case Youtube = 'youtube';
    case Vimeo = 'vimeo';
    case Dailymotion = 'dailymotion';

    public function check(string $url): bool
    {
        return match ($this) {
            self::Youtube => 1 === preg_match('/^https:\/\/www\.youtube\.com\/watch\?v=.+$/', $url),
            self::Vimeo => 1 === preg_match('/^https:\/\/vimeo\.com\/.+$/', $url),
            self::Dailymotion => 1 === preg_match('/^https:\/\/www\.dailymotion\.com\/video\/.+$/', $url),
        };
    }
}
