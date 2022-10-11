<?php

declare(strict_types=1);

namespace App\Twig\Component\DesignSystem;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('modal', template: 'components/design_system/modal.html.twig')]
final class ModalComponent
{
    public string $id;

    public string $title;

    public bool $closeButton = true;
}
