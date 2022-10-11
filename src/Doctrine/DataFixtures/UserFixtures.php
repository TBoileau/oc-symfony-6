<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

use function sprintf;

final class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private string $uploadsDir
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 15; ++$i) {
            $filename = sprintf('%s.png', Uuid::v4());
            copy(
                sprintf('%s/image.png', $this->uploadsDir),
                sprintf('%s/%s', $this->uploadsDir, $filename)
            );

            $user = (new User())
                ->setEmail(sprintf('user+%d@email.com', $i))
                ->setNickname(sprintf('user+%d', $i))
                ->setAvatar($filename);

            if ($i > 10) {
                $user->setRegistrationToken(Uuid::v4());
            }

            $manager->persist(
                $user->setPassword(
                    $this->userPasswordHasher->hashPassword($user, 'password')
                )
            );
        }

        $manager->flush();
    }
}
