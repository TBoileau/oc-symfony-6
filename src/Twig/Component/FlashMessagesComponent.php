<?php

declare(strict_types=1);

namespace App\Twig\Component;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('flash_messages')]
final class FlashMessagesComponent
{
    use DefaultActionTrait;

    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        /** @var Session $session */
        $session = $requestStack->getSession();

        $this->flashBag = $session->getFlashBag();
    }

    /**
     * @return array<string, array<string>>
     */
    public function getFlashes(): array
    {
        return $this->flashBag->all();
    }
}
