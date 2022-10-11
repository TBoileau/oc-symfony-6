<?php

declare(strict_types=1);

namespace App\UseCase\Trick;

use App\Doctrine\Entity\Trick;

interface UpdateTrickInterface
{
    public function __invoke(Trick $trick): void;
}
