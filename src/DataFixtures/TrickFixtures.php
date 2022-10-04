<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use App\Entity\VideoProvider;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use ReflectionProperty;

use function sprintf;

final class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var array<array-key, Category> $categories */
        $categories = $manager->getRepository(Category::class)->findAll();

        /** @var array<array-key, User> $users */
        $users = $manager->getRepository(User::class)->findBy(['registrationToken' => null]);

        $index = 1;

        foreach ($categories as $category) {
            foreach ($users as $user) {
                for ($i = 1; $i <= 5; ++$i) {
                    $trick = (new Trick())
                        ->setName(sprintf('Trick %d', $index))
                        ->setSlug(sprintf('trick-%d', $index))
                        ->setDescription(sprintf('Description %d', $index))
                        ->setCategory($category)
                        ->setUser($user)
                        ->setCover('image.png')
                        ->setUpdatedAt(new DateTimeImmutable('2022-01-01 00:00:00'))
                        ->addMedia(
                            (new Image())
                                ->setFilename('image.png')
                                ->setAlt(sprintf('Trick %d', $index))
                        )
                        ->addMedia(
                            (new Image())
                                ->setFilename('image.png')
                                ->setAlt(sprintf('Trick %d', $index))
                        )
                        ->addMedia(
                            (new Image())
                                ->setFilename('image.png')
                                ->setAlt(sprintf('Trick %d', $index))
                        )
                        ->addMedia(
                            (new Video())
                                ->setUrl('https://www.youtube.com/watch?v=ScMzIvxBSi4')
                                ->setProvider(VideoProvider::Youtube)
                        )
                        ->addMedia(
                            (new Video())
                                ->setUrl('https://www.youtube.com/watch?v=ScMzIvxBSi4')
                                ->setProvider(VideoProvider::Youtube)
                        )
                        ->addMedia(
                            (new Video())
                                ->setUrl('https://www.youtube.com/watch?v=ScMzIvxBSi4')
                                ->setProvider(VideoProvider::Youtube)
                        );

                    $reflectionProperty = new ReflectionProperty(Trick::class, 'createdAt');
                    $reflectionProperty->setValue($trick, new DateTimeImmutable('2022-01-01 00:00:00'));

                    $manager->persist($trick);

                    ++$index;
                }
            }
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class, UserFixtures::class];
    }
}
