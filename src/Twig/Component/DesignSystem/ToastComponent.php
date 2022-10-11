<?php

declare(strict_types=1);

namespace App\Twig\Component\DesignSystem;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('toast', template: 'components/design_system/toast.html.twig')]
final class ToastComponent
{
    public string $message;

    public string $type;

    public function getTitle(): string
    {
        return match ($this->type) {
            'success' => 'Succès',
            'error' => 'Erreur',
            'warning' => 'Attention',
            default => 'Information',
        };
    }

    public function getColor(): string
    {
        return match ($this->type) {
            'success' => 'success',
            'error' => 'danger',
            'warning' => 'warning',
            default => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this->type) {
            'success' => 'circle-check',
            'error' => 'circle-cross',
            'warning' => 'circle-exclamation',
            default => 'circle-info',
        };
    }
}
