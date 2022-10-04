<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Doctrine\Entity\Trick;
use App\Doctrine\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class TrickVoter extends Voter
{
    public const DELETE = 'delete';

    public function __construct(private readonly bool $deleteTrickByOwnerOnly)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::DELETE === $attribute && $subject instanceof Trick;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Trick $trick */
        $trick = $subject;

        return !$this->deleteTrickByOwnerOnly || $trick->getUser() === $user;
    }
}
