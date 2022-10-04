<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\Comment;
use App\Doctrine\Entity\Trick;
use App\Doctrine\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

use function sprintf;

final class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var array<array-key, Trick> $tricks */
        $tricks = $manager->getRepository(Trick::class)->findAll();

        /** @var array<array-key, User> $users */
        $users = $manager->getRepository(User::class)->findBy(['registrationToken' => null]);

        $index = 1;

        foreach ($tricks as $trick) {
            foreach ($users as $user) {
                $manager->persist(
                    (new Comment())
                        ->setUser($user)
                        ->setTrick($trick)
                        ->setContent(sprintf('Comment %d', $index))
                );

                ++$index;
            }
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [TrickFixtures::class];
    }
}
