<?php

declare(strict_types=1);

namespace App\UseCase\Trick;

use App\Doctrine\Entity\Trick;

interface CreateTrickInterface
{
    public function __invoke(Trick $trick): void;
}
