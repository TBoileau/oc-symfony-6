<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Entity\ResetPasswordRequest;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ResetPasswordRequestEmail implements EmailInterface
{
    public function build(TemplatedEmail $email, array $options = []): void
    {
        /** @var ResetPasswordRequest $resetPasswordRequest */
        $resetPasswordRequest = $options['reset_password_request'];

        $email
            ->to(
                new Address(
                    $resetPasswordRequest->getUser()->getEmail(),
                    $resetPasswordRequest->getUser()->getNickname()
                )
            )
            ->subject('Bienvenue sur le site de Snowtricks')
            ->htmlTemplate('emails/reset_password_request.html.twig')
            ->context(['reset_password_request' => $resetPasswordRequest])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['reset_password_request']);
        $resolver->setAllowedTypes('reset_password_request', ResetPasswordRequest::class);
    }
}
