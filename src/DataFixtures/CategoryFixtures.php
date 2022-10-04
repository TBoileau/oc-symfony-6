<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use function sprintf;

final class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $manager->persist((new Category())->setName(sprintf('Category %d', $i)));
        }

        $manager->flush();
    }
}
