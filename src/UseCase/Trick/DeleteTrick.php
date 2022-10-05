<?php

declare(strict_types=1);

namespace App\UseCase\Trick;

use App\Doctrine\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;

final class DeleteTrick implements DeleteTrickInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(Trick $trick): void
    {
        $this->entityManager->remove($trick);
        $this->entityManager->flush();
    }
}
