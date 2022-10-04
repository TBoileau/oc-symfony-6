<?php

declare(strict_types=1);

namespace App\UseCase\Trick;

use App\Entity\Trick;

interface DeleteInterface
{
    public function __invoke(Trick $trick): void;
}
