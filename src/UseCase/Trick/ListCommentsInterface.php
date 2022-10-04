<?php

declare(strict_types=1);

namespace App\UseCase\Trick;

use App\Doctrine\Entity\Comment;
use App\Doctrine\Entity\Trick;

interface ListCommentsInterface
{
    /**
     * @return array{
     *      page: int,
     *      pages: int,
     *      limit: int,
     *      total: int,
     *      count: int,
     *      _links: array{
     *          self: array{href: string},
     *          next?: array{href: string}
     *      },
     *      _embedded: array{tricks: array<array-key, Comment>}
     * }
     */
    public function __invoke(Trick $trick, int $page): array;
}
