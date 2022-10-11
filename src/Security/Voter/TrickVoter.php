<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Doctrine\Entity\Trick;
use App\Doctrine\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function in_array;

final class TrickVoter extends Voter
{
    public const DELETE = 'delete';
    public const UPDATE = 'update';

    public function __construct(
        private readonly bool $deleteTrickByOwnerOnly,
        private readonly bool $updateTrickByOwnerOnly
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::DELETE, self::UPDATE], true) && $subject instanceof Trick;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Trick $trick */
        $trick = $subject;

        return match ($attribute) {
            self::DELETE => !$this->deleteTrickByOwnerOnly || $trick->getUser() === $user,
            self::UPDATE => !$this->updateTrickByOwnerOnly || $trick->getUser() === $user,
            default => false,
        };
    }
}
