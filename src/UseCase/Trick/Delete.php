<?php

declare(strict_types=1);

namespace App\UseCase\Trick;

use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;

final class Delete implements DeleteInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(Trick $trick): void
    {
        $this->entityManager->remove($trick);
        $this->entityManager->flush();
    }
}
