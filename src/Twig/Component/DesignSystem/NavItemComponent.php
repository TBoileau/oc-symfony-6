<?php

declare(strict_types=1);

namespace App\Twig\Component\DesignSystem;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('nav_item', template: 'components/design_system/nav_item.html.twig')]
final class NavItemComponent
{
    private ?Request $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getMainRequest();
    }

    public string $label;

    public string $route;

    public function isActive(): bool
    {
        return null !== $this->request && $this->request->attributes->get('_route') === $this->route;
    }
}
