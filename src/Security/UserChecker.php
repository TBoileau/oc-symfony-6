<?php

declare(strict_types=1);

namespace App\Security;

use App\Doctrine\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return; // @codeCoverageIgnore
        }

        if (!$user->hasValidatedRegistration()) {
            throw new CustomUserMessageAccountStatusException('Vous devez valider votre inscription.');
        }
    }
}
