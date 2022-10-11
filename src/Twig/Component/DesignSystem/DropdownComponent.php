<?php

declare(strict_types=1);

namespace App\Twig\Component\DesignSystem;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('dropdown', template: 'components/design_system/dropdown.html.twig')]
final class DropdownComponent
{
    public string $tag = 'div';

    public string $class = '';

    public string $toggle = 'button';

    public string $toggleClass = 'btn btn-primary';

    public string $direction = 'end';

    /**
     * @var array<string, mixed>
     */
    public array $data = [];
}
