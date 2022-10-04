<?php

declare(strict_types=1);

namespace App\UseCase\Security;

use App\Doctrine\Entity\User;
use App\Mailer\Email\RegistrationEmail;
use App\Mailer\EmailSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

final class Register implements RegisterInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EmailSenderInterface $emailSender
    ) {
    }

    public function __invoke(User $user): void
    {
        /** @var string $plainPassword */
        $plainPassword = $user->getPlainPassword();

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));
        $user->setRegistrationToken(Uuid::v4());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->emailSender->with('user', $user)->send(RegistrationEmail::class);
    }
}
