<?php

declare(strict_types=1);

namespace App\UseCase\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class ValidRegistration implements ValidRegistrationInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(User $user): void
    {
        $user->setRegistrationToken(null);
        $this->entityManager->flush();
    }
}
