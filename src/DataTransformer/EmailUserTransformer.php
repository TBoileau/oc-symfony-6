<?php

declare(strict_types=1);

namespace App\DataTransformer;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<User, string>
 */
final class EmailUserTransformer implements DataTransformerInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function transform(mixed $value): string
    {
        return '';
    }

    public function reverseTransform($value): User
    {
        $user = $this->userRepository->findOneBy(['email' => $value]);

        if (!$user instanceof User) {
            throw new TransformationFailedException('Cette adresse email n\'existe pas.');
        }

        return $user;
    }
}
