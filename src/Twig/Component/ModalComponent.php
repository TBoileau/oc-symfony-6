<?php

declare(strict_types=1);

namespace App\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('modal')]
final class ModalComponent
{
    public string $id;

    public string $title;

    public bool $closeButton = true;
}
