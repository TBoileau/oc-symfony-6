<?php

declare(strict_types=1);

namespace App\Doctrine\Repository;

use App\Doctrine\Entity\Comment;
use App\Doctrine\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @return array<array-key, Comment>
     */
    public function getCommentsByTrickAndPage(Trick $trick, int $page): array
    {
        /** @var array<array-key, Comment> $tricks */
        $tricks = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->where('c.trick = :trick')
            ->setParameter('trick', $trick)
            ->setMaxResults(5)
            ->setFirstResult(($page - 1) * 5)
            ->getQuery()
            ->getResult()
        ;

        return $tricks;
    }
}
