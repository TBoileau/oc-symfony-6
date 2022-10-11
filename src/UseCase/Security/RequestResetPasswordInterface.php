<?php

declare(strict_types=1);

namespace App\UseCase\Security;

use App\Doctrine\Entity\ResetPasswordRequest;

interface RequestResetPasswordInterface
{
    public function __invoke(ResetPasswordRequest $resetPasswordRequest): void;
}
