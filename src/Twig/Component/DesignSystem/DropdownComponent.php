<?php

declare(strict_types=1);

namespace App\Twig\Component\DesignSystem;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('dropdown', template: 'components/design_system/dropdown.html.twig')]
final class DropdownComponent
{
    public string $direction = 'end';

    /**
     * @var array<string, mixed>
     */
    public array $data = [];
}
