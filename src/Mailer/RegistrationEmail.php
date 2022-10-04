<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RegistrationEmail implements EmailInterface
{
    public function build(TemplatedEmail $email, array $options = []): void
    {
        /** @var User $user */
        $user = $options['user'];

        $email
            ->to(new Address($user->getEmail(), $user->getNickname()))
            ->subject('Bienvenue sur le site de Snowtricks')
            ->htmlTemplate('emails/registration.html.twig')
            ->context(['user' => $user])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['user']);
        $resolver->setAllowedTypes('user', User::class);
    }
}
