<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\Doctrine\Entity\Video;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('video')]
final class VideoComponent
{
    public Video $video;
}
