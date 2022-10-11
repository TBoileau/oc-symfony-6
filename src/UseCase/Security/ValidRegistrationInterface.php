<?php

declare(strict_types=1);

namespace App\UseCase\Security;

use App\Doctrine\Entity\User;

interface ValidRegistrationInterface
{
    public function __invoke(User $user): void;
}
