<?php

declare(strict_types=1);

namespace App\Entity;

enum VideoProvider: string
{
    case Youtube = 'youtube';
    case Vimeo = 'vimeo';
    case Dailymotion = 'dailymotion';
}
