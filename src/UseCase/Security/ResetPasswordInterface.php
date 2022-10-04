<?php

declare(strict_types=1);

namespace App\UseCase\Security;

use App\Entity\ResetPasswordRequest;

interface ResetPasswordInterface
{
    public function __invoke(ResetPasswordRequest $resetPasswordRequest): void;
}
