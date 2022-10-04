<?php

declare(strict_types=1);

namespace App\UseCase\Security;

use App\Doctrine\Entity\ResetPasswordRequest;
use App\Mailer\Email\ResetPasswordRequestEmail;
use App\Mailer\EmailSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

final class RequestResetPassword implements RequestResetPasswordInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EmailSenderInterface $emailSender
    ) {
    }

    public function __invoke(ResetPasswordRequest $resetPasswordRequest): void
    {
        $resetPasswordRequest->setToken(Uuid::v4());

        $this->entityManager->persist($resetPasswordRequest);
        $this->entityManager->flush();

        $this->emailSender
            ->with('reset_password_request', $resetPasswordRequest)
            ->send(ResetPasswordRequestEmail::class);
    }
}
