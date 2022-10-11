<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\Comment;
use App\Doctrine\Entity\Trick;
use App\Doctrine\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    public function load(ObjectManager $manager): void
    {
        /** @var array<array-key, Trick> $tricks */
        $tricks = $manager->getRepository(Trick::class)->findAll();

        /** @var array<array-key, User> $users */
        $users = $manager->getRepository(User::class)->findBy(['registrationToken' => null]);

        $index = 1;

        foreach ($tricks as $trick) {
            foreach ($users as $user) {
                /** @var string $content */
                $content = $this->faker()->paragraphs(3, true);

                $manager->persist(
                    (new Comment())
                        ->setUser($user)
                        ->setTrick($trick)
                        ->setContent($content)
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
