<?php

declare(strict_types=1);

namespace App\UseCase\Trick;

use App\Entity\Trick;

interface ListTricksInterface
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
     *      _embedded: array{tricks: array<array-key, Trick>}
     * }
     */
    public function __invoke(int $page): array;
}
