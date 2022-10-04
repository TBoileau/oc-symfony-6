<?php

declare(strict_types=1);

namespace App\UseCase\Security;

use App\Entity\ResetPasswordRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class ResetPassword implements ResetPasswordInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function __invoke(ResetPasswordRequest $resetPasswordRequest): void
    {
        $user = $resetPasswordRequest->getUser();

        /** @var string $plainPassword */
        $plainPassword = $user->getPlainPassword();

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));

        $this->entityManager->persist($user);
        $this->entityManager->remove($resetPasswordRequest);
        $this->entityManager->flush();
    }
}
