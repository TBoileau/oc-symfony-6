<?php

declare(strict_types=1);

namespace App\Mailer;

interface EmailSenderInterface
{
    public function with(string $name, mixed $value): EmailSenderInterface;

    /**
     * @param class-string<EmailInterface> $emailClass
     */
    public function send(string $emailClass): void;
}
