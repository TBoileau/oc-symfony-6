<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function sprintf;

final class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; ++$i) {
            $user = (new User())
                ->setEmail(sprintf('user+%d@email.com', $i))
                ->setNickname(sprintf('user+%d', $i));

            $manager->persist(
                $user->setPassword(
                    $this->userPasswordHasher->hashPassword($user, 'password')
                )
            );
        }

        $manager->flush();
    }
}
