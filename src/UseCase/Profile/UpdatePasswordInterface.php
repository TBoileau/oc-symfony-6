<?php

declare(strict_types=1);

namespace App\UseCase\Profile;

use App\Doctrine\Entity\User;

interface UpdatePasswordInterface
{
    public function __invoke(User $user): void;
}
