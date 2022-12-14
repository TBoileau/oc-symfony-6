<?php

declare(strict_types=1);

namespace App\UseCase\Trick;

use App\Doctrine\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;

final class CommentTrick implements CommentTrickInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(Comment $comment): void
    {
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }
}
