<?php

declare(strict_types=1);

namespace App\Mailer\Email;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface EmailInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function build(TemplatedEmail $email, array $options = []): void;

    public function configureOptions(OptionsResolver $resolver): void;
}
