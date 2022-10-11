<?php

declare(strict_types=1);

namespace App\Twig\Component\DesignSystem;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('alert', template: 'components/design_system/alert.html.twig')]
final class AlertComponent
{
    public string $type;
}
